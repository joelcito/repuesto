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
                'nombre_producto' => 'required|string|max:255',
                'descripcion_producto' => 'nullable|string',
            ]);

            $usuario = Auth::user();

            $incorporacion = new Incorporacion();

            $incorporacion->producto_id = null;

            $incorporacion->nombre_producto =
                strtoupper($request->nombre_producto);

            $incorporacion->descripcion_producto =
                $request->descripcion_producto;

            $incorporacion->estado = 'ACTIVO';

            $incorporacion->usuario_creador_id =
                $usuario->id;

            $incorporacion->save();

            DB::commit();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Incorporación registrada correctamente'
            ]);

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