<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnidadController extends Controller
{
    public function listado()
    {
        return view('unidad.listado');
    }

    public function ajaxListado(Request $request)
    {

        if ($request->ajax()) {

            //SACAMOS EL LISTADO
            $unidades = Unidad::all();

            $valores = [
                'listado' => view('unidad.ajaxListado')->with(compact('unidades'))->render()
            ];

            $data = Respuesta::success($valores, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarUnidad(Request $request)
    {

        if ($request->ajax()) {

            //AL INICIO DECLARACION DE VARIABLES
            $unidad_id = $request->input('id');
            $nombre = $request->input('nombre');
            $usuario = Auth::user();

            if ($unidad_id == '0') {
                //LA CREACION DE UN NUEVa unidad
                $unidad = new Unidad();
                $unidad->usuario_creador_id = $usuario->id;

            } else {
                //LA EDICION DE UN NUEVO unidad
                $unidad = Unidad::find($unidad_id);
                $unidad->usuario_modificador_id = $usuario->id;
            }

            $unidad->nombre = $nombre;

            $unidad->estado = 1;
            $unidad->save();

            $data = Respuesta::success(null, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function eliminarUnidad(Request $request)
    {

        if ($request->ajax()) {

            //INICIALIZAMOS LAS VARIABLES
            $unidad_id = $request->input('unidad');
            $usuario = Auth::user();

            //BUSCAMOS AL unidad
            $unidad = Unidad::find($unidad_id);
            $unidad->usuario_eliminador_id = $usuario->id;
            $unidad->save();

            //AHORA ELIMINAMOS
            Unidad::destroy($unidad_id);

            $data = Respuesta::success(null, "Se elimino con exito");

        } else {

            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
