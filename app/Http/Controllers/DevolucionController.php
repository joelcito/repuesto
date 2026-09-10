<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\Movimiento;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Utils\Respuesta;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PDF;

class DevolucionController extends Controller
{
    public function listado()
    {
        $devoluciones = Devolucion::all();
        $ventas = Venta::with('cliente')->orderBy('id', 'desc')->get();
        $clientes = User::where('rol_id', 5)
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->get();
        $vendedores = User::where('rol_id', '!=', 5)
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->get();

        return view('devolucion.listado')->with(compact('devoluciones', 'ventas', 'clientes', 'vendedores'));

    }

    public function obtenerDetalleVenta(Request $request)
    {
        $detalle = VentaDetalle::with('producto')
            ->where('venta_id', $request->venta_id)
            ->get();
        return response()->json([
            'estado' => true,
            'data' => $detalle
        ]);
    }

    public function ajaxListado(Request $request)
    {
        $devoluciones = Devolucion::with('venta')
            ->latest('created_at')
            ->get();

        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => view(
                    'devolucion.ajaxListado',
                    compact('devoluciones')
                )->render()
            ]
        ]);
    }

    public function guardarDevolucion(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'venta_id' => 'required',
                'productos' => 'required|array|min:1',
                'tipo' => 'required',
                'motivo' => 'required',
                'metodo_pago' => 'required_if:tipo,DINERO'
            ]);

            $usuario = Auth::user();
            $venta = Venta::with('caja')->findOrFail($request->venta_id);

            if ($request->tipo == 'CAMBIO') {
                throw new \Exception('Cambio de producto aún no implementado');
            }

            $totalGeneral = 0;

            foreach ($request->productos as $item) {

                $ventaDetalle = VentaDetalle::where('venta_id', $venta->id)
                    ->where('producto_id', $item['producto_id'])
                    ->first();

                if (!$ventaDetalle) {
                    throw new \Exception('Producto no encontrado en la venta');
                }

                $cantidadDevuelta = $ventaDetalle->cantidad_devuelta ?? 0;
                $disponible = $ventaDetalle->cantidad - $cantidadDevuelta;

                if ($item['cantidad'] > $disponible) {
                    throw new \Exception('Cantidad excede lo vendido');
                }


                $descuentoUnitario =
                    ($ventaDetalle->descuento ?? 0)
                    / $ventaDetalle->cantidad;

                $subtotal =
                    ($item['cantidad'] * $ventaDetalle->precio_unitario)
                    - ($descuentoUnitario * $item['cantidad']);

                $totalGeneral += $subtotal;
            }


            $devolucion = Devolucion::create([
                'venta_id' => $venta->id,
                'caja_id' => $venta->caja_id,
                'usuario_cliente_id' => $venta->usuario_cliente_id,
                'tipo' => $request->tipo,
                'metodo_pago' => $request->tipo == 'DINERO'
                    ? $request->metodo_pago
                    : null,
                'motivo' => $request->motivo,
                'total' => $totalGeneral,
                'estado' => 'INGRESO',
                'usuario_creador_id' => $usuario->id
            ]);


            foreach ($request->productos as $item) {

                $ventaDetalle = VentaDetalle::where('venta_id', $venta->id)
                    ->where('producto_id', $item['producto_id'])
                    ->first();
                $producto = Producto::findOrFail($item['producto_id']);
                $descuentoUnitario =
                    ($ventaDetalle->descuento ?? 0)
                    / $ventaDetalle->cantidad;

                $descuento =
                    $descuentoUnitario * $item['cantidad'];

                $subtotal =
                    ($item['cantidad'] * $ventaDetalle->precio_unitario)
                    - $descuento;

                DevolucionDetalle::create([
                    'devolucion_id' => $devolucion->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $ventaDetalle->precio_unitario,
                    'descuento' => $descuento,
                    'subtotal' => $subtotal,
                    'usuario_creador_id' => $usuario->id
                ]);

                $ventaDetalle->cantidad_devuelta =
                    ($ventaDetalle->cantidad_devuelta ?? 0) + $item['cantidad'];

                $ventaDetalle->save();
                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $venta->caja->sucursal_id,
                    'tipo' => 'DEVOLUCION',
                    'cantidad' => $item['cantidad'],
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $ventaDetalle->precio_unitario,
                    'fecha' => now(),
                    'descripcion' => 'DEVOLUCION #' . $devolucion->id,
                    'estado' => 'INGRESO',
                    'usuario_creador_id' => $usuario->id
                ]);
            }

            if ($request->tipo == 'DINERO') {

                MovimientoCaja::create([
                    'caja_id' => $venta->caja_id,
                    'venta_id' => $venta->id,
                    'tipo' => 'SALIDA',
                    'metodo_pago' => $request->metodo_pago,
                    'monto' => $devolucion->total,
                    'descripcion' => 'DEVOLUCION #' . $devolucion->id,
                    'fecha' => now(),
                    'estado' => 'SALIDA',
                    'usuario_creador_id' => $usuario->id
                ]);

                Pago::create([
                    'usuario_creador_id' => $usuario->id,
                    'venta_id' => $venta->id,
                    'fecha' => now(),
                    'sucursal_id' => $venta->caja->sucursal_id,
                    'monto' => $devolucion->total,
                    'tipo_pago' => $request->metodo_pago,
                    'descripcion' => 'DEVOLUCION #' . $devolucion->id,
                    'estado' => 'SALIDA',
                    'caja_id' => $venta->caja_id
                ]);

                $venta->caja->increment(
                    'total_egresos',
                    $devolucion->total
                );
            }

            $pendientes = VentaDetalle::where('venta_id', $venta->id)
                ->whereRaw('cantidad > COALESCE(cantidad_devuelta,0)')
                ->count();

            // $totalDevuelto = DevolucionDetalle::where('devolucion_id', $devolucion->id)
            //     ->sum('subtotal');
            // $venta->total = max(0, $venta->total - $totalDevuelto);

            $venta->estado = $pendientes == 0
                ? 'DEVUELTO'
                : 'DEVOLUCION_PARCIAL';

            $venta->save();



            DB::commit();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Devolución registrada correctamente'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public function eliminarDevolucion(Request $request)
    {
        DB::beginTransaction();
        try {
            $devolucion = Devolucion::findOrFail($request->id);
            if ($devolucion->estado == 'ANULADO') {
                throw new \Exception(
                    'La devolución ya fue anulada'
                );
            }
            $venta = Venta::findOrFail(
                $devolucion->venta_id
            );
            $usuario = Auth::user();
            $movimientos = Movimiento::where(
                'descripcion',
                'DEVOLUCION #' . $devolucion->id
            )->get();
            foreach ($movimientos as $movimiento) {
                $ventaDetalle = VentaDetalle::where(
                    'venta_id',
                    $venta->id
                )
                    ->where(
                        'producto_id',
                        $movimiento->producto_id
                    )
                    ->first();

                if ($ventaDetalle) {

                    $ventaDetalle->cantidad_devuelta =
                        max(
                            0,
                            ($ventaDetalle->cantidad_devuelta ?? 0)
                            - $movimiento->cantidad
                        );

                    $ventaDetalle->save();
                }
                Movimiento::create([
                    'producto_id' => $movimiento->producto_id,
                    'sucursal_id' => $movimiento->sucursal_id,
                    'tipo' => 'ANULACION_DEVOLUCION',
                    'cantidad' => $movimiento->cantidad,
                    'precio_compra' => $movimiento->precio_compra,
                    'precio_venta' => $movimiento->precio_venta,
                    'motivo' => 'ANULACION DEVOLUCION',
                    'fecha' => now(),
                    'descripcion' => 'ANULACION DEVOLUCION #' . $devolucion->id,
                    'estado' => 'SALIDA',
                    'usuario_creador_id' => $usuario->id
                ]);
            }

            if ($devolucion->tipo == 'DINERO') {
                MovimientoCaja::create([
                    'caja_id' => $venta->caja_id,
                    'venta_id' => $venta->id,
                    'tipo' => 'INGRESO',
                    'metodo_pago' => $devolucion->metodo_pago,
                    'monto' => $devolucion->total,
                    'descripcion' =>
                        'ANULACION DEVOLUCION #' .
                        $devolucion->id,
                    'fecha' => now(),
                    'estado' => 'INGRESO',
                    'usuario_creador_id' => $usuario->id
                ]);

                Pago::create([
                    'usuario_creador_id' => $usuario->id,
                    'venta_id' => $venta->id,
                    'sucursal_id' => $venta->caja->sucursal_id,
                    'monto' => $devolucion->total,
                     'caja_id' => $venta->caja_id,
                    'cambio' => 0,
                    'fecha' => now(),
                    'descripcion' =>
                        'ANULACION DEVOLUCION',
                    'tipo_pago' => $devolucion->metodo_pago,
                    'estado' => 'INGRESO'
                ]);

                $venta->caja->decrement(
                    'total_egresos',
                    min(
                        $venta->caja->total_egresos,
                        $devolucion->total
                    )
                );
            }
            $devolucion->estado = 'ANULADO';
            $devolucion->usuario_eliminador_id = $usuario->id;
            $devolucion->save();


            // $detallesPendientes =
            //     VentaDetalle::where(
            //         'venta_id',
            //         $venta->id
            //     )
            //         ->whereRaw(
            //             'cantidad > COALESCE(cantidad_devuelta,0)'
            //         )
            //         ->count();
            // if ($detallesPendientes == 0) {
            //     $venta->estado = 'DEVUELTO';
            // } else {
            //     $venta->estado =
            //         'DEVOLUCION_PARCIAL';
            // }

            $totalDevueltoVenta = VentaDetalle::where('venta_id', $venta->id)
                ->sum('cantidad_devuelta');

            $totalVendidoVenta = VentaDetalle::where('venta_id', $venta->id)
                ->sum('cantidad');

            if ($totalDevueltoVenta <= 0) {
                $venta->estado = 'SALIDA';
            } elseif ($totalDevueltoVenta >= $totalVendidoVenta) {
                $venta->estado = 'DEVUELTO';
            } else {
                $venta->estado = 'DEVOLUCION_PARCIAL';
            }

            $venta->save();
            DB::commit();
            return response()->json([
                'estado' => true,
                'mensaje' =>
                    'Devolución anulada correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public function detalledevolucion($devolucion_id)
    {
        $usuario = Auth::user();
        $devolucion = Devolucion::with([
            'venta.cliente',
            'venta.detalles.producto',
            'caja'
        ])->find($devolucion_id);
        if (!$devolucion) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Devolución no encontrada'
                );
        }

        $data = [
            'usuario' => $usuario,
            'devolucion' => $devolucion
        ];

        $pdf = Pdf::loadView(
            'devolucion.pdf.detalledevolucion',
            $data
        )->setPaper('a5', 'landscape');

        return $pdf->stream(
            'devolucion_' .
            $devolucion->id .
            '.pdf'
        );
    }



    public function buscarVentas(Request $request)
    {
        $query = Venta::with([
            'cliente',
            'usuario'
        ]);
        if ($request->numero) {
            $query->where('id', $request->numero);
        }

        if ($request->cliente) {
            $query->where(
                'usuario_cliente_id',
                $request->cliente
            );
        }

        if ($request->vendedor) {
            $query->where(
                'usuario_creador_id',
                $request->vendedor
            );
        }

        if ($request->desde) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->desde
            );
        }

        if ($request->hasta) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->hasta
            );
        }

        $ventas = $query
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'estado' => true,
            'html' => view(
                'devolucion.ajaxVentas',
                compact('ventas')
            )->render()
        ]);

    }
}
