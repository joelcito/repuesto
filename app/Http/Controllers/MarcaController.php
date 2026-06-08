<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarcaController extends Controller
{
    public function listado()
    {
        return view('marca.listado');
    }

    public function ajaxListado(Request $request)
    {

        if ($request->ajax()) {

            //SACAMOS EL LISTADO
            $marcas = Marca::all();

            $valores = [
                'listado' => view('marca.ajaxListado')->with(compact('marcas'))->render()
            ];

            $data = Respuesta::success($valores, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarMarca(Request $request)
    {
        if ($request->ajax()) {
            //AL INICIO DECLARACION DE VARIABLES
            $marca_id = $request->input('id');
            $nombre = $request->input('nombre');
            $usuario = Auth::user();

            if ($marca_id == '0') {
                //LA CREACION DE UN NUEVa PROVEEDOR
                $marca = new Marca();
                $marca->usuario_creador_id = $usuario->id;

            } else {
                //LA EDICION DE UN NUEVO marca
                $marca = Marca::find($marca_id);
                $marca->usuario_modificador_id = $usuario->id;
            }

            $marca->nombre = $nombre;
            $marca->estado = 1;
            $marca->save();

            $data = Respuesta::success(null, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function eliminarMarca(Request $request)
    {

        if ($request->ajax()) {

            //INICIALIZAMOS LAS VARIABLES
            $marca_id = $request->input('marca');
            $usuario = Auth::user();

            //BUSCAMOS AL marca
            $marca = Marca::find($marca_id);
            $marca->usuario_eliminador_id = $usuario->id;
            $marca->save();

            //AHORA ELIMINAMOS
            Marca::destroy($marca_id);

            $data = Respuesta::success(null, "Se elimino con exito");

        } else {

            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
