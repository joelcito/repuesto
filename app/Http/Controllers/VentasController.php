<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Movimiento;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Utils\Respuesta;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Log;
use PDF;

class VentasController extends Controller
{
    public function listado()
    {
        $clientes = User::get();
        $productos = Producto::where('estado', 1)->get();
        $cajas = Caja::where('estado', 'ABIERTA')->get();
        return view(
            'venta.listado',
            compact(
                'clientes',
                'productos',
                'cajas'
            )
        );
    }

    public function ajaxListado(Request $request)
    {
        $query = Venta::with([
            'cliente',
            'usuarioCreador',
            'pagos'
        ]);
        if ($request->filled('buscar_nro_factura')) {
            $query->where(
                'numero_factura',
                $request->buscar_nro_factura
            );
        }
        if ($request->filled('buscar_nombre_cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where(
                    'nombres',
                    'LIKE',
                    '%' . $request->buscar_nombre_cliente . '%'
                );
            });
        }
        if (
            $request->filled('buscar_fecha_inicio')
            &&
            $request->filled('buscar_fecha_fin')
        ) {
            $query->whereBetween('fecha', [
                $request->buscar_fecha_inicio . ' 00:00:00',
                $request->buscar_fecha_fin . ' 23:59:59'
            ]);
        }
        $ventas = $query
            ->orderBy('id', 'desc')
            ->get();
        $listado = view(
            'venta.ajaxListado',
            compact('ventas')
        )->render();
        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }

    public function guardarVenta(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'cliente_id' => 'required',
                'caja_id' => 'required',
                'metodo_pago' => 'required',
                'productos' => 'required|array|min:1',
                'pagos' => 'required|array|min:1'
            ]);

            $usuario = Auth::user();

            $caja = Caja::where('id', $request->caja_id)
                ->where('estado', 'ABIERTA')
                ->first();

            if (!$caja) {
                throw new \Exception('La caja no está abierta');
            }

            $numeroFactura = Venta::max('numero_factura');
            $numeroFactura = $numeroFactura ? $numeroFactura + 1 : 1;

            $pagos = $request->pagos ?? [];

            $venta = new Venta();
            $venta->usuario_creador_id = $usuario->id;
            $venta->usuario_cliente_id = $request->cliente_id;
            $venta->usuario_venta_id = $usuario->id;
            $venta->caja_id = $request->caja_id;
            $venta->fecha = $request->fecha;
            $venta->numero_factura = $numeroFactura;
            $venta->nit = $request->nit;
            $venta->razon_social = $request->razon_social;
            $venta->subtotal = 0;
            $venta->descuento = $request->descuento ?? 0;
            $venta->total = 0;

            $venta->descripcion = $request->descripcion;
            $venta->observacion = $request->observacion;
            $venta->metodo_pago = collect($pagos)
                ->pluck('metodo')
                ->unique()
                ->implode(',');
            $venta->estado = 'SALIDA';


            $venta->save();

            $subtotalGeneral = 0;

            foreach ($request->productos as $item) {

                $producto = Producto::find($item['producto_id']);

                if (!$producto) {
                    throw new \Exception("Producto no encontrado");
                }

                $stockActual = $this->obtenerStock(
                    $producto->id,
                    $caja->sucursal_id
                );


                if ($stockActual < $item['cantidad']) {

                    throw new \Exception(
                        "Stock insuficiente para: " .
                        $producto->nombre .
                        ". Disponible: " .
                        $stockActual
                    );
                }

                $precio = $item['precio'];

                $subtotal =
                    ($precio * $item['cantidad'])
                    - ($item['descuento'] ?? 0);


                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'cantidad_devuelta' => 0,
                    'precio_compra' => $producto->precio_compra,
                    'precio_original' => $producto->precio_venta,
                    'precio_unitario' => $precio,
                    'descuento' => $item['descuento'] ?? 0,
                    'tipo_precio' => $item['tipo_precio'],
                    'subtotal' => $subtotal,
                    'descripcion' => $item['descripcion'] ?? null,
                    'estado' => 'SALIDA',
                    'usuario_creador_id' => $usuario->id
                ]);
                // SOLO MOVIMIENTO
                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $caja->sucursal_id,
                    'tipo' => 'VENTA',
                    'cantidad' => $item['cantidad'],
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $precio,
                    'compra_ingreso' => null,
                    'motivo' => 'VENTA',
                    'fecha' => now(),
                    'descripcion' => 'VENTA #' . $venta->id,
                    'estado' => 'SALIDA',
                    'usuario_creador_id' => $usuario->id
                ]);

                $subtotalGeneral += $subtotal;
            }

            $total = $subtotalGeneral - $venta->descuento;


            $venta->subtotal = $subtotalGeneral;
            $venta->total = $total;
            $venta->save();
            // $montoPagado = $request->monto_pagado ?? $total;

            $pagos = $request->pagos ?? [];

            if (count($pagos) == 0) {
                throw new \Exception("Debe registrar al menos un pago");
            }

            $totalPagado = collect($pagos)->sum(function ($p) {
                return ($p['monto'] ?? 0) - ($p['descuento'] ?? 0);
            });


            $cambio = max(0, $totalPagado - $total);


            if ($totalPagado > $total) {
                $cambio = $totalPagado - $total;
            }

            $montoReal = $totalPagado - $cambio;

            if ($montoReal >= $total) {
                $venta->estado_pago = 'PAGADO';
            } elseif ($montoReal > 0) {
                $venta->estado_pago = 'PARCIAL';
            } else {
                $venta->estado_pago = 'DEUDA';
            }



            foreach ($pagos as $pago) {

                if (!isset($pago['monto']) || $pago['monto'] <= 0) {
                    continue;
                }
                $monto = ($pago['monto'] ?? 0);
                $descuento = ($pago['descuento'] ?? 0);
                $montoNeto = $monto - $descuento;
                Pago::create([
                    'usuario_creador_id' => $usuario->id,
                    'venta_id' => $venta->id,
                    'caja_id' => $caja->id,
                    'sucursal_id' => $caja->sucursal_id,

                    'monto' => $monto,
                    'cambio' => max(0, $monto - $descuento - $total), // opcional o simplificado

                    'fecha' => now(),
                    'descripcion' => 'PAGO VENTA #' . $venta->numero_factura,
                    'tipo_pago' => $pago['metodo'],
                    'estado' => 'INGRESO'
                ]);


                MovimientoCaja::create([
                    'caja_id' => $venta->caja_id,
                    'venta_id' => $venta->id,
                    'tipo' => 'INGRESO',
                    'metodo_pago' => $pago['metodo'],
                    'monto' => $montoNeto,
                    'descripcion' => 'VENTA #' . $venta->id,
                    'fecha' => now(),
                    'estado' => 'INGRESO',
                    'usuario_creador_id' => $usuario->id
                ]);

                $caja->total_ingresos += $montoNeto;
            }
            $caja->save();

            DB::commit();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Venta registrada correctamente',
                'venta_id' => $venta->id
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public function detalle($venta_id)
    {
        $venta = Venta::with([
            'cliente',
            'usuarioCreador',
            'detalles.producto',
            'pagos'
        ])->find($venta_id);
        if (!$venta) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }
        return view('venta.detalle', compact('venta'));
    }

    public function detalleCliente(Request $request, $venta_id)
    {
        $usuario = Auth::user();
        $venta = Venta::with(
            ['cliente', 'detalles.producto', 'pagos']
        )->find($venta_id);
        if (!$venta) {
            return response()->view(
                'errors.custom',
                ['code' => 404, 'title' => 'VENTA NO ENCONTRADA', 'message' => 'La venta no existe'],
                404
            );
        }
        if ($venta->usuario_cliente_id != $usuario->id) {
            return response()->view(
                'errors.custom',
                [
                    'code' => 403,
                    'title' => 'NO AUTORIZADO',
                    'message' => 'No autorizado'
                ],
                403
            );
        }
        return view('venta.detalleCliente', compact('venta'));
    }

    public function ajaxListadoFacturasCliente(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['estado' => false, 'mensaje' => 'No existe']);
        }
        $usuario = Auth::user();
        $query = Venta::with(['cliente', 'detalles.producto', 'pagos'])->where('usuario_cliente_id', $usuario->id);
        if ($request->filled('buscar_nro_factura')) {
            $query->where(
                'numero_factura',
                $request->buscar_nro_factura
            );
        }
        if ($request->filled('buscar_fecha_inicio') && $request->filled('buscar_fecha_fin')) {
            $query->whereBetween('fecha', [
                $request->buscar_fecha_inicio . ' 00:00:00',
                $request->buscar_fecha_fin . ' 23:59:59'
            ]);
        }
        $ventas = $query->orderBy('id', 'desc')->get();
        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => view(
                    'venta.ajaxListadoFacturasCliente',
                    compact('ventas')
                )->render()
            ]
        ]);
    }

    public function registrarPago(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate(['venta_id' => 'required', 'caja_id' => 'required', 'monto' => 'required|numeric|min:0.01', 'tipo_pago' => 'required']);
            $usuario = Auth::user();
            $venta = Venta::find($request->venta_id);
            if (!$venta) {
                throw new \Exception('Venta no encontrada');
            }
            $caja = Caja::where('id', $request->caja_id)->where('estado', 'ABIERTA')->first();
            if (!$caja) {
                throw new \Exception('Caja no disponible');
            }
            $totalPagado = $venta->pagos()->where('estado', 'INGRESO')->sum('monto');
            $saldoPendiente = $venta->total - $totalPagado;
            if ($request->monto > $saldoPendiente) {
                throw new \Exception('El monto excede la deuda');
            }
            Pago::create([
                'usuario_creador_id' => $usuario->id,
                'venta_id' => $venta->id,
                'caja_id' => $caja->id,
                'sucursal_id' => $caja->sucursal_id,
                'monto' => $request->monto,
                'cambio' => 0,
                'fecha' => now(),
                'descripcion' => 'ABONO VENTA #' . $venta->numero_factura,
                'tipo_pago' => $request->tipo_pago,
                'estado' => 'INGRESO'
            ]);

            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'venta_id' => $venta->id,
                'tipo' => 'INGRESO',
                'metodo_pago' => $request->tipo_pago,
                'monto' => $request->monto,
                'descripcion' => 'ABONO VENTA #' . $venta->numero_factura,
                'fecha' => now(),
                'estado' => 'INGRESO',
                'usuario_creador_id' => $usuario->id
            ]);

            $caja->total_ingresos += $request->monto;
            $caja->save();
            $nuevoTotalPagado = $venta->pagos()->where('estado', 'INGRESO')->sum('monto');
            if ($nuevoTotalPagado >= $venta->total) {
                $venta->estado_pago = 'PAGADO';
            } else {
                $venta->estado_pago = 'PARCIAL';
            }
            $venta->save();
            DB::commit();
            return response()->json(['estado' => true, 'mensaje' => 'Pago registrado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage()]);
        }
    }
    public function anularVenta(Request $request)
    {

        if (auth()->user()->esOperador()) {
            abort(403, 'No tiene permisos para realizar esta acción.');
        }
        DB::beginTransaction();
        try {
            $venta = Venta::with(['detalles.producto', 'pagos'])->find($request->venta_id);
            if (!$venta) {
                throw new \Exception('Venta no encontrada');
            }
            if ($venta->estado == 'ANULADO') {
                throw new \Exception('La venta ya fue anulada');
            }
            $usuario = Auth::user();
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                if ($producto) {

                    Movimiento::create([
                        'producto_id' => $producto->id,
                        'sucursal_id' => $venta->caja->sucursal_id,
                        'tipo' => 'ANULACION_VENTA',
                        'cantidad' => $detalle->cantidad,
                        'precio_compra' => $producto->precio_compra,
                        'precio_venta' => $detalle->precio_unitario,
                        'motivo' => 'ANULACION VENTA',
                        'fecha' => now(),
                        'descripcion' => 'ANULACION VENTA #' . $venta->id,
                        'estado' => 'INGRESO',
                        'usuario_creador_id' => $usuario->id
                    ]);
                }
            }

            foreach ($venta->pagos as $pago) {

                $caja = Caja::find($pago->caja_id);

                if ($caja) {

                    $caja->total_ingresos -= $pago->monto;

                    // evitar negativos
                    if ($caja->total_ingresos < 0) {
                        $caja->total_ingresos = 0;
                    }

                    $caja->save();
                }
            }

            Pago::where('venta_id', $venta->id)->update(['estado' => 'ANULADO']);
            MovimientoCaja::where('venta_id', $venta->id)->update(['estado' => 'ANULADO']);
            $venta->estado = 'ANULADO';
            $venta->usuario_modificador_id = $usuario->id;
            $venta->save();
            DB::commit();
            return response()->json(['estado' => true, 'mensaje' => 'Venta anulada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage()]);
        }
    }
    // public function recibo($venta_id)
    // {
    //     $usuario = Auth::user();
    //     $venta = Venta::with(['cliente', 'detalles.producto', 'pagos'])->find($venta_id);
    //     if (!$venta) {
    //         return redirect()->back()->with('error', 'Venta no encontrada');
    //     }
    //     $data = [
    //         'usuario' => $usuario,
    //         'venta' => $venta
    //     ];
    //     $pdf = Pdf::loadView('venta.pdf.recibo', $data)->setPaper('a5', 'landscape');
    //     return $pdf->stream('recibo.pdf');
    // }

    public function recibo($venta_id)
    {
        $usuario = Auth::user();

        $venta = Venta::with(['cliente', 'detalles.producto', 'pagos'])
            ->find($venta_id);

        if (!$venta) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }

        $totalPagado = $venta->pagos
            ->where('estado', 'INGRESO')
            ->sum('monto');

        $saldo = max(0, $venta->total - $totalPagado);
        $cambio = max(0, $totalPagado - $venta->total);

        $data = [
            'usuario' => $usuario,
            'venta' => $venta,
            'totalPagado' => $totalPagado,
            'saldo' => $saldo,
            'cambio' => $cambio
        ];

        $pdf = Pdf::loadView('venta.pdf.recibo', $data)
            ->setPaper('a5', 'landscape');

        return $pdf->stream('recibo.pdf');
    }


    public function tiquet($venta_id)
    {

        $usuario = Auth::user();

        $venta = Venta::with(['cliente', 'detalles.producto', 'pagos'])
            ->find($venta_id);

        if (!$venta) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }

        $totalPagado = $venta->pagos
            ->where('estado', 'INGRESO')
            ->sum('monto');

        $saldo = max(0, $venta->total - $totalPagado);
        $cambio = max(0, $totalPagado - $venta->total);

        $data = [
            'usuario' => $usuario,
            'venta' => $venta,
            'totalPagado' => $totalPagado,
            'saldo' => $saldo,
            'cambio' => $cambio
        ];

        $pdf = Pdf::loadView('venta.pdf.tiquet', $data)
            ->setPaper([0, 0, 226.77, 800], 'portrait');

        return $pdf->stream('tiquet.pdf');
    }

    public function formulario()
    {
        $rolCliente = 5;
        $clientes = User::where('rol_id', $rolCliente)->get();
        $productos = Producto::where('estado', 1)->get();
        $cajas = Caja::where('estado', 'ABIERTA')->get();
        $usuario = Auth::user();
        $usuarios = User::all();
        return view(
            'venta.formulario',
            compact(
                'clientes',
                'productos',
                'cajas',
                'usuario',
                'usuarios'
            )
        );
    }

    public function ajaxListadoDetalleVenta(Request $request)
    {
        $venta = Venta::with([
            'detalles.producto'
        ])->find($request->venta_id);
        $listado = view(
            'venta.ajaxListadoDetalle',
            compact('venta')
        )->render();
        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }

    // public function buscarProductos(Request $request)
    // {
    //     $buscar = $request->buscar;
    //     $productos = Producto::with('marca', 'imagenes')
    //         ->where('estado', 1)
    //         ->where(function ($query) use ($buscar) {
    //             $query->where('nombre', 'LIKE', "%{$buscar}%")
    //                 ->orWhere('codigo_barras', 'LIKE', "%{$buscar}%")
    //                 ->orWhere('codigo_interno', 'LIKE', "%{$buscar}%")
    //                 ->orWhere('descripcion', 'LIKE', "%{$buscar}%")
    //                 ->orWhere('vehiculos_compatibles', 'LIKE', "%{$buscar}%")
    //                 ->orWhere('numero_parte_vehiculo', 'LIKE', "%{$buscar}%")

    //                 ->orWhereHas('marca', function ($q) use ($buscar) {
    //                     $q->where('nombre', 'LIKE', "%{$buscar}%");
    //                 });
    //         })
    //         ->limit(30)
    //         ->get();

    //     return response()->json($productos);
    // }

    public function buscarProductos(Request $request)
    {
        $buscar = $request->buscar;

        $productos = Producto::with('marca', 'imagenes')
            ->where('estado', 1)
            ->where(function ($query) use ($buscar) {
                $query->where('nombre', 'LIKE', "%{$buscar}%")
                    ->orWhere('codigo_barras', 'LIKE', "%{$buscar}%")
                    ->orWhere('codigo_interno', 'LIKE', "%{$buscar}%")
                    ->orWhere('descripcion', 'LIKE', "%{$buscar}%")
                    ->orWhere('vehiculos_compatibles', 'LIKE', "%{$buscar}%")
                    ->orWhere('numero_parte_vehiculo', 'LIKE', "%{$buscar}%")
                    ->orWhereHas('marca', function ($q) use ($buscar) {
                        $q->where('nombre', 'LIKE', "%{$buscar}%");
                    });
            })
            ->limit(30)
            ->get();

        $usuario = Auth::user();

        foreach ($productos as $producto) {

            $producto->stock_actual = $this->obtenerStock(
                $producto->id,
                $usuario->sucursal_id
            );
        }

        return response()->json($productos);
    }


    public function obtenerStock($productoId, $sucursalId)
    {
        $ingresos = Movimiento::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->whereIn('tipo', [
                'INGRESO',
                'DEVOLUCION',
                'TRANSFERENCIA_INGRESO',
                'ANULACION_VENTA'
            ])
            ->sum('cantidad');

        $salidas = Movimiento::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->whereIn('tipo', [
                'SALIDA',
                'VENTA',
                'TRANSFERENCIA_SALIDA'
            ])
            ->sum('cantidad');

        return $ingresos - $salidas;
    }

}

