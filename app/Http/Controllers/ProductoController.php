<?php

namespace App\Http\Controllers;

use App\Models\Incorporacion;
use App\Models\Producto;
use App\Models\ProductoImagen;
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
        $categorias = Categoria::where('estado', 1)
            ->whereIn('tipo', ['AUTOMOVIL', 'MOTOCICLETA'])
            ->doesntHave('children')
            ->get();

        $proveedores = Proveedor::where('estado', 1)->get();
        $sucursales = Sucursal::where('estado', 1)->get();
        $marcas = Marca::where('estado', 1)->get();
        $unidades = Unidad::where('estado', 1)->get();
        $productos = Producto::with('imagenes')->get();

        return view('producto.listado', compact(
            'categorias',
            'proveedores',
            'sucursales',
            'marcas',
            'unidades'
        ));

    }

    // public function ajaxListado(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $productos = Producto::with([
    //             'categoria',
    //             'proveedor',
    //             'sucursal',
    //             'marca',
    //             'unidad',
    //             'imagenes'
    //         ])
    //             ->withSum([
    //                 'movimientos as ingresos' => function ($q) {
    //                     $q->where('tipo', 'INGRESO');
    //                 }
    //             ], 'cantidad')
    //             ->withSum([
    //                 'movimientos as salidas' => function ($q) {
    //                     $q->where('tipo', 'SALIDA');
    //                 }
    //             ], 'cantidad')
    //             ->latest()
    //             ->get()
    //             ->map(function ($p) {

    //                 $p->stock_actual =
    //                     ($p->ingresos ?? 0) - ($p->salidas ?? 0);

    //                 return $p;
    //             });



    //         $valores = [
    //             'listado' => view('producto.ajaxListado')
    //                 ->with(compact('productos'))
    //                 ->render()
    //         ];
    //         $data = Respuesta::success($valores, "Datos obtenidos correctamente");

    //     } else {
    //         $data = Respuesta::error(null, "Error al obtener los datos");
    //     }
    //     return $data;
    // }


    public function ajaxListado(Request $request)
    {
        $query = Producto::with([
            'categoria',
            'marca',
            'imagenes'
        ])
            ->withSum(['movimientos as ingresos' => fn($q) => $q->where('tipo', 'INGRESO')], 'cantidad')
            ->withSum(['movimientos as salidas' => fn($q) => $q->where('tipo', 'SALIDA')], 'cantidad');

        // BUSCAR
        if ($request->buscar) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                    ->orWhere('codigo_barras', 'like', "%{$request->buscar}%")
                    ->orWhere('codigo_interno', 'like', "%{$request->buscar}%");
            });
        }

        // CATEGORIA
        $query->when($request->categoria, function ($q) use ($request) {
            $q->where('categoria_id', $request->categoria);
        });

        // MARCA
        $query->when($request->marca, function ($q) use ($request) {
            $q->where('marca_id', $request->marca);
        });


        // ESTADO
        if ($request->estado !== null && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }

        $productos = $query->get();

        foreach ($productos as $p) {
            $p->stock_actual = ($p->ingresos ?? 0) - ($p->salidas ?? 0);
        }

        // STOCK
        if ($request->stock == 'con') {
            $productos = $productos->where('stock_actual', '>', 0);
        }

        if ($request->stock == 'sin') {
            $productos = $productos->where('stock_actual', '<=', 0);
        }

        return Respuesta::success([
            'listado' => view('producto.ajaxListado', compact('productos'))->render()
        ]);
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

                'precio_venta' => 'required|numeric',
                'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
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

            $producto->stock_minimo = $request->input('stock_minimo');
            $producto->unidad_id = $request->input('unidad_id');
            $producto->precio_compra = $request->input('precio_compra');
            $producto->precio_venta = $request->input('precio_venta');
            $producto->precio_mayor = $request->input('precio_mayor');
            //$producto->compra_ingreso = $request->input('compra_ingreso');
            $producto->sucursal_id = $request->input('sucursal_id');
            $producto->proveedor_id = $request->input('proveedor_id');
            $producto->observaciones = $request->input('observaciones');
            $producto->medidas = $request->input('medidas');

            $producto->estado = 1;
            $producto->save();

            if ($request->input('incorporacion_id')) {

                $inc = Incorporacion::find($request->input('incorporacion_id'));

                if ($inc) {
                    $inc->estado = 'PROCESADO';
                    $inc->producto_id = $producto->id;
                    $inc->save();
                }
            }

            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $index => $archivo) {
                    $nombre = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
                    $archivo->move(public_path('imagenes/productos'), $nombre);
                    ProductoImagen::create([
                        'producto_id' => $producto->id,
                        'imagen' => $nombre,
                        'orden' => $index + 1,
                        'estado' => 1,
                        'usuario_creador_id' => Auth::id()
                    ]);
                }
            }

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

    public function obtenerProducto($id)
    {
        return Producto::find($id);
    }


}