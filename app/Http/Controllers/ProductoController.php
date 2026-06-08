<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Categoria;
use App\Models\Sucursal;
use App\Models\Proveedor;
use App\Models\Marca;
use App\Models\Unidad;


class ProductoController extends Controller
{
    public function listado()
    {
        $categorias = Categoria::where('estado', 1)->get();
        $proveedores = Proveedor::where('estado', 1)->get();
        $sucursales = Sucursal::where('estado', 1)->get();
        $marcas = Marca::where('estado', 1)->get();
        $unidades = Unidad::where('estado', 1)->get();

        return view('producto.listado', compact(
            'categorias',
            'proveedores',
            'sucursales',
            'marcas',
            'unidades'
        ));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $productos = Producto::with([
                'categoria',
                'proveedor',
                'sucursal',
                'marca',
                'unidad'
            ])->latest()->get();
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

    public function generarCodigo()
    {
        $ultimo = Producto::orderBy('id', 'desc')->first();
        $numero = $ultimo ? $ultimo->id + 1 : 1;
        $codigo = 'REP-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
        return response()->json($codigo);
    }


    public function guardarProducto(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'nombre' => 'required',
                'categoria_id' => 'required',
                'marca_id' => 'required',
                'unidad_id' => 'required',
                'stock_actual' => 'required|numeric',
                'precio_venta' => 'required|numeric',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);
            $producto_id = $request->input('id');
            $usuario = Auth::user();

            if ($producto_id == '0') {
                $producto = new Producto();
                $producto->usuario_creador_id = $usuario->id;
            } else {
                $producto = Producto::find($producto_id);
                $producto->usuario_modificador_id = $usuario->id;
            }

            $producto->codigo_barras = $request->input('codigo_barras');
            $producto->codigo_interno = $request->input('codigo_interno');
            $producto->nombre = $request->input('nombre');
            $producto->descripcion = $request->input('descripcion');
            $producto->vehiculos_compatibles = $request->input('vehiculos_compatibles');
            $producto->categoria_id = $request->input('categoria_id');
            $producto->marca_id = $request->input('marca_id');
            //$producto->vehiculo = $request->input('vehiculo');
            $producto->numero_parte_vehiculo = $request->input('numero_parte_vehiculo');
            $producto->stock_actual = $request->input('stock_actual');
            $producto->stock_minimo = $request->input('stock_minimo');
            $producto->unidad_id = $request->input('unidad_id');
            $producto->precio_compra = $request->input('precio_compra');
            $producto->precio_venta = $request->input('precio_venta');
            $producto->precio_mayor = $request->input('precio_mayor');
            $producto->compra_ingreso = $request->input('compra_ingreso');
            $producto->sucursal_id = $request->input('sucursal_id');
            $producto->proveedor_id = $request->input('proveedor_id');
            $producto->observaciones = $request->input('observaciones');
            if ($request->hasFile('imagen')) {
                if ($producto->imagen != null) {
                    $rutaAnterior = public_path(
                        'imagenes/productos/' . $producto->imagen
                    );
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }

                $archivo = $request->file('imagen');
                $nombreImagen = time() . '.' .
                    $archivo->getClientOriginalExtension();
                $archivo->move(
                    public_path('imagenes/productos'),
                    $nombreImagen
                );
                $producto->imagen = $nombreImagen;
            }
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