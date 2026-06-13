<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Devolucion;
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
        return view('devolucion.listado')->with(compact('devoluciones', 'ventas'));

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
        if ($request->ajax()) {
            $devoluciones = Devolucion::with('venta')
                ->whereNull('deleted_at')
                ->orderBy('id', 'desc')
                ->get();
            $valores = [
                'listado' => view('devolucion.ajaxListado')
                    ->with(compact('devoluciones'))
                    ->render()
            ];

            return response()->json([
                'estado' => true,
                'data' => $valores
            ]);
        }
    }

    public function guardarDevolucion(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'venta_id' => 'required',
                'productos' => 'required|array|min:1',
                'tipo' => 'required',
                'motivo' => 'required'
            ]);
            $usuario = Auth::user();
            $venta = Venta::findOrFail($request->venta_id);
            if ($request->tipo == 'CAMBIO') {
                throw new \Exception(
                    'Cambio de producto aún no implementado'
                );
            }
            $totalGeneral = 0;
            foreach ($request->productos as $item) {
                $ventaDetalle = VentaDetalle::where(
                    'venta_id',
                    $request->venta_id
                )
                    ->where(
                        'producto_id',
                        $item['producto_id']
                    )
                    ->first();

                if (!$ventaDetalle) {
                    throw new \Exception(
                        'Producto no encontrado en la venta'
                    );
                }

                $cantidadDevuelta = $ventaDetalle->cantidad_devuelta ?? 0;
                $cantidadDisponible = $ventaDetalle->cantidad - $cantidadDevuelta;
                if ($item['cantidad'] > $cantidadDisponible) {
                    throw new \Exception(
                        'Cantidad excede lo vendido'
                    );
                }
                $subtotal =
                    $item['cantidad'] *
                    $ventaDetalle->precio_unitario;
                $totalGeneral += $subtotal;
            }

            $devolucion = new Devolucion();
            $devolucion->venta_id = $request->venta_id;
            $devolucion->caja_id = $venta->caja_id;
            $devolucion->usuario_cliente_id = $venta->usuario_cliente_id;
            $devolucion->tipo = $request->tipo;
            $devolucion->motivo = $request->motivo;
            $devolucion->total = $totalGeneral;
            $devolucion->estado = 'INGRESO';
            $devolucion->usuario_creador_id = $usuario->id;
            $devolucion->save();
            foreach ($request->productos as $item) {
                $ventaDetalle = VentaDetalle::where(
                    'venta_id',
                    $request->venta_id
                )
                    ->where(
                        'producto_id',
                        $item['producto_id']
                    )
                    ->first();

                $producto = Producto::findOrFail(
                    $item['producto_id']
                );

                $subtotal =
                    $item['cantidad'] *
                    $ventaDetalle->precio_unitario;
                if (
                    $request->tipo == 'DINERO' ||
                    $request->tipo == 'PRODUCTO'
                ) {

                    $ventaDetalle->cantidad_devuelta =
                        ($ventaDetalle->cantidad_devuelta ?? 0)
                        + $item['cantidad'];

                    $ventaDetalle->save();
                }

                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $venta->caja->sucursal_id,
                    'tipo' => 'DEVOLUCION',
                    'cantidad' => $item['cantidad'],
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $ventaDetalle->precio_unitario,
                    'motivo' => 'DEVOLUCION',
                    'fecha' => now(),
                    'descripcion' => 'DEVOLUCION #' . $devolucion->id,
                    'estado' => 'INGRESO',
                    'usuario_creador_id' => $usuario->id
                ]);

                if ($request->tipo == 'DINERO') {

                    MovimientoCaja::create([
                        'caja_id' => $venta->caja_id,
                        'venta_id' => $venta->id,
                        'tipo' => 'SALIDA',
                        'metodo_pago' => $venta->metodo_pago,
                        'monto' => $subtotal,
                        'descripcion' => 'DEVOLUCION #' . $devolucion->id,
                        'fecha' => now(),
                        'estado' => 'SALIDA',
                        'usuario_creador_id' => $usuario->id
                    ]);

                    Pago::create([
                        'usuario_creador_id' => $usuario->id,
                        'venta_id' => $venta->id,
                        'sucursal_id' => $venta->caja->sucursal_id,
                        'monto' => $subtotal,
                        'cambio' => 0,
                        'fecha' => now(),
                        'descripcion' => 'DEVOLUCION',
                        'tipo_pago' => $venta->metodo_pago,
                        'estado' => 'SALIDA'
                    ]);
                    $caja = Caja::find($venta->caja_id);

                    $caja->total_egresos =
                        $caja->total_egresos + $subtotal;

                    $caja->save();

                }
            }

            $detallesPendientes = VentaDetalle::where(
                'venta_id',
                $venta->id
            )
                ->whereRaw(
                    'cantidad > COALESCE(cantidad_devuelta,0)'
                )
                ->count();
            if ($detallesPendientes == 0) {
                $venta->estado = 'DEVUELTO';
            } else {
                $venta->estado = 'DEVOLUCION_PARCIAL';
            }

            $venta->save();

            DB::commit();
            return response()->json([
                'estado' => true,
                'mensaje' =>
                    'Devolución registrada correctamente'
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
                        $ventaDetalle->cantidad_devuelta
                        - $movimiento->cantidad;
                    if (
                        $ventaDetalle->cantidad_devuelta < 0
                    ) {
                        $ventaDetalle->cantidad_devuelta = 0;
                    }
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
                    'metodo_pago' => $venta->metodo_pago,
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
                    'cambio' => 0,
                    'fecha' => now(),
                    'descripcion' =>
                        'ANULACION DEVOLUCION',
                    'tipo_pago' => $venta->metodo_pago,
                    'estado' => 'INGRESO'
                ]);

                $caja = Caja::find($venta->caja_id);
                $caja->total_egresos = $caja->total_egresos - $devolucion->total;
                if ($caja->total_egresos < 0) {
                    $caja->total_egresos = 0;
                }
                $caja->save();
            }
            $devolucion->estado = 'ANULADO';
            $devolucion->usuario_eliminador_id = $usuario->id;
            $devolucion->save();
            $devolucion->delete();
            $detallesPendientes =
                VentaDetalle::where(
                    'venta_id',
                    $venta->id
                )
                    ->whereRaw(
                        'cantidad > COALESCE(cantidad_devuelta,0)'
                    )
                    ->count();

            if ($detallesPendientes == 0) {
                $venta->estado = 'DEVUELTO';
            } else {
                $venta->estado =
                    'DEVOLUCION_PARCIAL';
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
}

