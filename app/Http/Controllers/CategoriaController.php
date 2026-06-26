<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    public function listado()
    {
        $categoriasPadre = Categoria::whereNull('parent_id')->get();

        return view('categoria.listado', compact('categoriasPadre'));
    }
    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {

            $categorias = Categoria::with('parent')->get();
            $valores = [
                'listado' => view('categoria.ajaxListado')->with(compact('categorias'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardar(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'nombre' => 'required',
                'tipo' => 'required',
            ]);

            $id = $request->input('id');

            $nombre = $request->input('nombre');
            $tipo = $request->input('tipo');
            $descripcion = $request->input('descripcion');
            $parent_id = $request->parent_id ?: null;
            $usuario = Auth::user();

            if ($id == 0) {
                $categoria = new Categoria();
                $categoria->usuario_creador_id = $usuario->id;
            } else {
                $categoria = Categoria::find($id);
                $categoria->usuario_modificador_id = $usuario->id;
            }

            $categoria->nombre = $nombre;
            $categoria->descripcion = $descripcion;
            $categoria->tipo = $tipo;
            $categoria->parent_id = $parent_id;
            $categoria->estado = 1;

            $categoria->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminar(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $usuario = Auth::user();
            $categoria = Categoria::find($id);
            $categoria->usuario_eliminador_id = $usuario->id;
            $categoria->save();
            Categoria::destroy($id);
            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
