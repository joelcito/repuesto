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
                'productos' => 'required|array|min:1'
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
            $venta->metodo_pago = $request->metodo_pago;
            $venta->descripcion = $request->descripcion;
            $venta->observacion = $request->observacion;
            $venta->estado = 'INGRESO';
            $venta->usuario_creador_id = $usuario->id;
            $venta->save();
            $subtotalGeneral = 0;

            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);
                if (!$producto) {
                    throw new \Exception("Producto no encontrado");
                }
                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente de: " . $producto->nombre);
                }
                $precio = $item['precio'];
                $subtotal = $precio * $item['cantidad'] - ($item['descuento'] ?? 0);
                $detalle = new VentaDetalle();
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $producto->id;
                $detalle->cantidad = $item['cantidad'];
                $detalle->cantidad_devuelta = 0;
                $detalle->precio_compra = $producto->precio_compra;
                $detalle->precio_original = $producto->precio_venta;
                $detalle->precio_unitario = $precio;
                $detalle->descuento = $item['descuento'] ?? 0;
                $detalle->tipo_precio = $item['tipo_precio'];
                $detalle->subtotal = $subtotal;
                $detalle->descripcion = $item['descripcion'] ?? null;
                $detalle->estado = 'INGRESO';
                $detalle->usuario_creador_id = $usuario->id;
                $detalle->save();
                // DESCONTAR STOCK
                $producto->stock_actual =
                    $producto->stock_actual - $item['cantidad'];
                $producto->save();

                // MOVIMIENTO STOCK
                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $producto->sucursal_id,
                    'tipo' => 'VENTA',
                    'cantidad' => $item['cantidad'],
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $precio,
                    'compra_ingreso' => null,
                    'motivo' => 'VENTA',
                    'fecha' => now(),
                    'descripcion' => 'VENTA #' . $venta->id,
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => $usuario->id
                ]);
                $subtotalGeneral += $subtotal;
            }

            $total = $subtotalGeneral - $venta->descuento;
            $venta->subtotal = $subtotalGeneral;
            $venta->total = $total;
            $montoPagado = $request->monto_pagado ?? $total;

            $cambio = 0;

            if ($montoPagado > $total) {
                $cambio = $montoPagado - $total;
            }
            $montoReal = $montoPagado - $cambio;
            // ESTADO PAGO 
            if ($montoReal >= $total) {
                $venta->estado_pago = 'PAGADO';
            } elseif ($montoReal > 0) {
                $venta->estado_pago = 'PARCIAL';
            } else {
                $venta->estado_pago = 'DEUDA';
            }
            $venta->save();

            if ($montoReal > 0) {
                Pago::create([
                    'usuario_creador_id' => $usuario->id,
                    'venta_id' => $venta->id,
                    'caja_id' => $caja->id,
                    'sucursal_id' => $caja->sucursal_id,
                    'monto' => $montoReal,
                    'cambio' => $cambio,
                    'fecha' => now(),
                    'descripcion' => 'PAGO VENTA #' . $venta->numero_factura,
                    'tipo_pago' => $venta->metodo_pago,
                    'estado' => 'ACTIVO'
                ]);
                // MOVIMIENTO CAJA 
                MovimientoCaja::create([
                    'caja_id' => $venta->caja_id,
                    'venta_id' => $venta->id,
                    'tipo' => 'INGRESO',
                    'metodo_pago' => $venta->metodo_pago,
                    'monto' => $montoReal,
                    'descripcion' => 'VENTA #' . $venta->id,
                    'fecha' => now(),
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => $usuario->id
                ]);
                // ACTUALIZAR CAJA 
                $caja->total_ingresos += $montoReal;
                $caja->save();
            }
            DB::commit();
            return response()->json(['estado' => true, 'mensaje' => 'Venta registrada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage()]);
        }
    }


    /*----------*/


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
        // FILTRO NUMERO FACTURA 
        if ($request->filled('buscar_nro_factura')) {
            $query->where(
                'numero_factura',
                $request->buscar_nro_factura
            );
        }
        // FILTRO FECHAS 
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
            $totalPagado = $venta->pagos()->where('estado', 'ACTIVO')->sum('monto');
            $saldoPendiente = $venta->total - $totalPagado;
            if ($request->monto > $saldoPendiente) {
                throw new \Exception('El monto excede la deuda');
            } // CREAR PAGO 
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
                'estado' => 'ACTIVO'
            ]);
            // MOVIMIENTO CAJA 
            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'venta_id' => $venta->id,
                'tipo' => 'INGRESO',
                'metodo_pago' => $request->tipo_pago,
                'monto' => $request->monto,
                'descripcion' => 'ABONO VENTA #' . $venta->numero_factura,
                'fecha' => now(),
                'estado' => 'ACTIVO',
                'usuario_creador_id' => $usuario->id
            ]);
            // ACTUALIZAR CAJA
            $caja->total_ingresos += $request->monto;
            $caja->save();
            // NUEVO TOTAL PAGADO 
            $nuevoTotalPagado = $venta->pagos()->where('estado', 'ACTIVO')->sum('monto');
            // ACTUALIZAR ESTADO 
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
            // DEVOLVER STOCK 
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                if ($producto) {
                    $producto->stock_actual += $detalle->cantidad;
                    $producto->save();
                    Movimiento::create([
                        'producto_id' => $producto->id,
                        'sucursal_id' => $producto->sucursal_id,
                        'tipo' => 'ANULACION_VENTA',
                        'cantidad' => $detalle->cantidad,
                        'precio_compra' => $producto->precio_compra,
                        'precio_venta' => $detalle->precio_unitario,
                        'motivo' => 'ANULACION VENTA',
                        'fecha' => now(),
                        'descripcion' => 'ANULACION VENTA #' . $venta->id,
                        'estado' => 'ACTIVO',
                        'usuario_creador_id' => $usuario->id
                    ]);
                }
            }
            // ANULAR PAGOS
            Pago::where('venta_id', $venta->id)->update(['estado' => 'ANULADO']);
            // ANULAR MOVIMIENTO CAJA 
            MovimientoCaja::where('venta_id', $venta->id)->update(['estado' => 'ANULADO']);
            // ANULAR VENTA 
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
    public function recibo($venta_id)
    {
        $usuario = Auth::user();
        $venta = Venta::with(['cliente', 'detalles.producto', 'pagos'])->find($venta_id);
        if (!$venta) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }
        $data = [
            'usuario' => $usuario,
            'venta' => $venta
        ];
        $pdf = Pdf::loadView('venta.pdf.recibo', $data)->setPaper('a5', 'landscape');
        return $pdf->stream('recibo.pdf');
    }


    public function formulario()
    {
        $rolCliente = 3;

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

}

