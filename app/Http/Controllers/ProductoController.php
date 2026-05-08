<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function listado()
    {
        return view('producto.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {

            // LISTADO DE PRODUCTOS
            $productos = Producto::all();

            $valores = [
                'listado' => view('producto.ajaxListado')
                    ->with(compact('productos'))
                    ->render()
            ];

            $data = Respuesta::success($valores, "Datos obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;
    }

    public function guardarProducto(Request $request)
    {
        if ($request->ajax()) {

            // VARIABLES
            $producto_id = $request->input('id');
            $usuario = Auth::user();

            if ($producto_id == '0') {
                $producto = new Producto();
                $producto->usuario_creador_id = $usuario->id;
            } else {
                $producto = Producto::find($producto_id);
                $producto->usuario_modificador_id = $usuario->id;
            }

            // DATOS PRODUCTO
            $producto->codigo_barras = $request->input('codigo_barras');
            $producto->codigo_interno = $request->input('codigo_interno');
            $producto->nombre = $request->input('nombre');
            $producto->descripcion = $request->input('descripcion');

            $producto->categoria = $request->input('categoria');
            $producto->marca = $request->input('marca');
            $producto->numero_parte_vehiculo = $request->input('numero_parte_vehiculo');

            $producto->stock_actual = $request->input('stock_actual');
            $producto->stock_minimo = $request->input('stock_minimo');
            $producto->unidad = $request->input('unidad');

            $producto->precio_compra = $request->input('precio_compra');
            $producto->precio_venta = $request->input('precio_venta');
            $producto->precio_mayor = $request->input('precio_mayor');

            $producto->ubicacion = $request->input('ubicacion');
            $producto->proveedor = $request->input('proveedor');

            $producto->observaciones = $request->input('observaciones');
            $producto->imagen = $request->input('imagen');
            $producto->estado = 1;

            $producto->save();

            $data = Respuesta::success(null, "Producto guardado correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;
    }

    public function eliminarProducto(Request $request)
    {
        if ($request->ajax()) {

            $producto_id = $request->input('producto');
            $usuario = Auth::user();

            $producto = Producto::find($producto_id);

            // auditoría eliminación (soft manual)
            $producto->usuario_eliminador_id = $usuario->id;
            $producto->save();

            Producto::destroy($producto_id);

            $data = Respuesta::success(null, "Producto eliminado con éxito");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;
    }

}