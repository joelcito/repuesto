<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{


    public function listado()
    {
        $sucursales = Sucursal::all();
        return view('movimiento.listado')->with(compact('sucursales'));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {

            $productoId = $request->productoId;

            $sucursales = Sucursal::with('movimientos')->get();


            $valores = [
                'stock' => view('movimiento.ajaxListado')
                    ->with(compact(
                        'sucursales',
                        'productoId'
                    ))
                    ->render()
            ];

            return Respuesta::success($valores, "Datos obtenidos correctamente");
        }

        return Respuesta::error(null, "Error");
    }

    /**
     * GUARDAR INGRESO
     */
    public function guardarIngreso(Request $request)
    {

        if ($request->ajax()) {
            $producto_id = $request->idProd;
            $sucursal_id = $request->idSuc;
            $request->validate([

                'producto_id' => 'required',
                'sucursal_id' => 'required',
                'cantidad' => 'required|numeric|min:1',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',

            ]);

            $usuario = Auth::user();

            // MOVIMIENTO

            $movimiento = new Movimiento();
            $movimiento->usuario_creador_id = $usuario->id;
            $movimiento->producto_id = $producto_id;
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->tipo = 'ingreso';
            $movimiento->cantidad = $request->cantidad;
            $movimiento->precio_compra = $request->precio_compra;
            $movimiento->precio_venta = $request->precio_venta;
            $movimiento->fecha = now();
            $movimiento->descripcion = $request->descripcion;
            $movimiento->estado = 1;
            $movimiento->save();

            // ACTUALIZAR STOCK PRODUCTO
            $producto = Producto::find($request->producto_id);
            $producto->stock_actual =
                $producto->stock_actual + $request->cantidad;

            // ACTUALIZAR PRECIOS
            $producto->precio_compra = $request->precio_compra;
            $producto->precio_venta = $request->precio_venta;
            $producto->save();

            return Respuesta::success(null, "Ingreso registrado correctamente");
        }

        return Respuesta::error(null, "Error");
    }

    /**
     * GUARDAR SALIDA
     */
    public function guardarSalida(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->idProds;
            $sucursal_id = $request->idSucs;
            $request->validate([
                'producto_id' => 'required',
                'sucursal_id' => 'required',
                'cantidad' => 'required|numeric|min:1',
                'motivo' => 'required',
            ]);

            $producto = Producto::find($request->producto_id);

            // VALIDAR STOCK
            if ($request->cantidad > $producto->stock_actual) {
                return Respuesta::error(
                    null,
                    "La salida es mayor al stock disponible"
                );
            }

            $usuario = Auth::user();

            // MOVIMIENTO
            $movimiento = new Movimiento();
            $movimiento->usuario_creador_id = $usuario->id;
            $movimiento->producto_id = $producto_id;
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->tipo = 'salida';
            $movimiento->cantidad = $request->cantidad;
            $movimiento->motivo = $request->motivo;
            $movimiento->fecha = now();
            $movimiento->descripcion = $request->descripcion;
            $movimiento->estado = 1;
            $movimiento->save();

            // DESCONTAR STOCK
            $producto->stock_actual =
                $producto->stock_actual - $request->cantidad;
            $producto->save();

            return Respuesta::success(null, "Salida registrada correctamente");
        }

        return Respuesta::error(null, "Error");
    }

    /**
     * ELIMINAR MOVIMIENTO
     */
    public function eliminarMovimiento(Request $request)
    {

        if ($request->ajax()) {

            $movimiento = Movimiento::find($request->movimiento_id);
            $usuario = Auth::user();
            $movimiento->usuario_eliminador_id = $usuario->id;
            $movimiento->deleted_at = now();
            $movimiento->save();
            return Respuesta::success(null, "Movimiento eliminado");
        }

        return Respuesta::error(null, "Error");
    }

}