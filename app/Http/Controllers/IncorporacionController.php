<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Incorporacion;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;
use App\Utils\Respuesta;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IncorporacionController extends Controller
{

    public function listado()
    {
        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get();
        return view('incorporacion.listado')->with(compact('productos'));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $incorporaciones = Incorporacion::with('producto')->orderBy('id', 'desc')->get();
            $valores = ['listado' => view('incorporacion.ajaxListado')->with(compact('incorporaciones'))->render()];
            return response()->json(['estado' => true, 'data' => $valores]);
        }
    }
    public function guardarIncorporacion(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'producto_id' => 'required',
                'cantidad' => 'required|numeric|min:1',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
            ]);
            $usuario = Auth::user();
            $producto = Producto::find($request->producto_id);

            if (!$producto) {
                throw new \Exception('Producto no encontrado');
            }
            $incorporacion = new Incorporacion();
            $incorporacion->producto_id = $producto->id;
            $incorporacion->sucursal_id = $producto->sucursal_id;
            $incorporacion->cantidad = $request->cantidad;
            $incorporacion->precio_compra = $request->precio_compra;
            $incorporacion->precio_venta = $request->precio_venta;
            $incorporacion->motivo = $request->motivo;
            $incorporacion->descripcion = $request->descripcion;
            $incorporacion->estado = 'ACTIVO';
            $incorporacion->usuario_creador_id = $usuario->id;
            $incorporacion->save();
            // AUMENTAR STOCK 
            $producto->stock_actual = $producto->stock_actual + $request->cantidad;
            $producto->precio_compra = $request->precio_compra;
            $producto->precio_venta = $request->precio_venta;
            $producto->save();
            // MOVIMIENTO 
            Movimiento::create([
                'producto_id' => $producto->id,
                'sucursal_id' => $producto->sucursal_id,
                'tipo' => 'INCORPORACION',
                'cantidad' => $request->cantidad,
                'precio_compra' => $request->precio_compra,
                'precio_venta' => $request->precio_venta,
                'motivo' => 'INCORPORACION',
                'fecha' => now(),
                'descripcion' =>
                    'INCORPORACION #' . $incorporacion->id,
                'estado' => 'ACTIVO',
                'usuario_creador_id' => $usuario->id
            ]);
            DB::commit();
            return response()->json(['estado' => true, 'mensaje' => 'Incorporación registrada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
    public function eliminarIncorporacion(Request $request)
    {

        DB::beginTransaction();

        try {

            $incorporacion = Incorporacion::find($request->id);

            if (!$incorporacion) {
                throw new \Exception('Registro no encontrado');
            }

            $producto = Producto::find($incorporacion->producto_id);

            // DEVOLVER STOCK

            $producto->stock_actual =
                $producto->stock_actual -
                $incorporacion->cantidad;

            if ($producto->stock_actual < 0) {
                $producto->stock_actual = 0;
            }

            $producto->save();

            $incorporacion->delete();

            DB::commit();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Registro eliminado'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }


}