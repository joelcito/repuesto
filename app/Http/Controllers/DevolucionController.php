<?php

namespace App\Http\Controllers;

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

                $cantidadDevuelta =
                    $ventaDetalle->cantidad_devuelta ?? 0;

                $cantidadDisponible =
                    $ventaDetalle->cantidad -
                    $cantidadDevuelta;

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

            $devolucion->venta_id =
                $request->venta_id;

            $devolucion->caja_id =
                $venta->caja_id;

            $devolucion->usuario_cliente_id =
                $venta->usuario_cliente_id;

            $devolucion->tipo =
                $request->tipo;

            $devolucion->motivo =
                $request->motivo;

            $devolucion->total =
                $totalGeneral;

            $devolucion->estado =
                'ACTIVO';

            $devolucion->usuario_creador_id =
                $usuario->id;

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

                    $producto->stock_actual =
                        $producto->stock_actual +
                        $item['cantidad'];

                    $producto->save();
                }

                $ventaDetalle->cantidad_devuelta =
                    ($ventaDetalle->cantidad_devuelta ?? 0)
                    + $item['cantidad'];

                $ventaDetalle->save();


                Movimiento::create([

                    'producto_id' =>
                        $producto->id,

                    'sucursal_id' =>
                        $producto->sucursal_id,

                    'tipo' =>
                        'DEVOLUCION',

                    'cantidad' =>
                        $item['cantidad'],

                    'precio_compra' =>
                        $producto->precio_compra,

                    'precio_venta' =>
                        $ventaDetalle->precio_unitario,

                    'motivo' =>
                        'DEVOLUCION',

                    'fecha' =>
                        now(),

                    'descripcion' =>
                        'DEVOLUCION #' . $devolucion->id,

                    'estado' =>
                        'ACTIVO',

                    'usuario_creador_id' =>
                        $usuario->id
                ]);

                if ($request->tipo == 'DINERO') {
                    MovimientoCaja::create([

                        'caja_id' =>
                            $venta->caja_id,

                        'venta_id' =>
                            $venta->id,

                        'tipo' =>
                            'EGRESO',

                        'metodo_pago' =>
                            $venta->metodo_pago,

                        'monto' =>
                            $subtotal,

                        'descripcion' =>
                            'DEVOLUCION #' . $devolucion->id,

                        'fecha' =>
                            now(),

                        'estado' =>
                            'ACTIVO',

                        'usuario_creador_id' =>
                            $usuario->id
                    ]);

                    Pago::create([

                        'usuario_creador_id' =>
                            $usuario->id,

                        'venta_id' =>
                            $venta->id,

                        'sucursal_id' =>
                            $venta->sucursal_id,

                        'monto' =>
                            $subtotal,

                        'cambio' =>
                            0,

                        'fecha' =>
                            now(),

                        'descripcion' =>
                            'DEVOLUCION',

                        'tipo_pago' =>
                            $venta->metodo_pago,

                        'estado' =>
                            'EGRESO'
                    ]);
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

                $venta->save();
            }

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

    public function eliminar(Request $request)
    {
        $devolucion = Devolucion::find($request->id);
        $devolucion->delete();
        return response()->json([
            'estado' => true,
            'mensaje' => 'Registro eliminado'
        ]);
    }
}

