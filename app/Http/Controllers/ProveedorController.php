<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    public function listado()
    {
        return view('proveedor.listado');
    }

    public function ajaxListado(Request $request)
    {

        if ($request->ajax()) {

            //SACAMOS EL LISTADO
            $proveedores = Proveedor::all();

            $valores = [
                'listado' => view('proveedor.ajaxListado')->with(compact('proveedores'))->render()
            ];

            $data = Respuesta::success($valores, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarProveedor(Request $request)
    {

        if ($request->ajax()) {

            //AL INICIO DECLARACION DE VARIABLES
            $proveedor_id = $request->input('id');
            $nombre_completo = $request->input('nombre_completo');
            $nit = $request->input('nit');
            $razon_social = $request->input('razon_social');
            $direccion = $request->input('direccion');
            $celular = $request->input('celular');
            $usuario = Auth::user();

            if ($proveedor_id == '0') {
                //LA CREACION DE UN NUEVa PROVEEDOR
                $proveedor = new Proveedor();
                $proveedor->usuario_creador_id = $usuario->id;

            } else {
                //LA EDICION DE UN NUEVO proveedor
                $proveedor = Proveedor::find($proveedor_id);
                $proveedor->usuario_modificador_id = $usuario->id;
            }

            $proveedor->nombre_completo = $nombre_completo;
            $proveedor->nit = $nit;
            $proveedor->razon_social = $razon_social;
            $proveedor->direccion = $direccion;
            $proveedor->celular = $celular;
            $proveedor->save();

            $data = Respuesta::success(null, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function eliminarProveedor(Request $request)
    {

        if ($request->ajax()) {

            //INICIALIZAMOS LAS VARIABLES
            $proveedor_id = $request->input('proveedor');
            $usuario = Auth::user();

            //BUSCAMOS AL Proveedor
            $proveedor = Proveedor::find($proveedor_id);
            $proveedor->usuario_eliminador_id = $usuario->id;
            $proveedor->save();

            //AHORA ELIMINAMOS
            Proveedor::destroy($proveedor_id);

            $data = Respuesta::success(null, "Se elimino con exito");

        } else {

            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
