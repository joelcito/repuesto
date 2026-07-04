<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Incorporacion;
use App\Models\Marca;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\Unidad;
use App\Models\User;
use App\Utils\Respuesta;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Models\IncorporacionImagen;
class IncorporacionController extends Controller
{
    public function listado()
    {
        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::where('estado', 1)
            ->whereIn('tipo', ['AUTOMOVIL', 'MOTOCICLETA'])
            ->doesntHave('children')
            ->get();


        $proveedores = Proveedor::where('estado', 1)->get();
        $sucursales = Sucursal::where('estado', 1)->get();
        $marcas = Marca::where('estado', 1)->get();
        $unidades = Unidad::where('estado', 1)->get();

        return view('incorporacion.listado', compact(
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
            $incorporaciones = Incorporacion::with('producto', 'imagenes')
                ->whereNull('deleted_at')
                ->orderBy('id', 'desc')->get();
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
                'medidas' => 'nullable|string',
            ]);
            $usuario = Auth::user();
            $incorporacion = new Incorporacion();
            $incorporacion->producto_id = null;
            $incorporacion->nombre_producto =
                strtoupper($request->nombre_producto);
            $incorporacion->descripcion_producto =
                $request->descripcion_producto;

            $incorporacion->estado = 'ACTIVO';
            $incorporacion->medidas = $request->medidas;
            $incorporacion->usuario_creador_id =
                $usuario->id;
            $incorporacion->save();
            if ($request->hasFile('imagenes')) {

                foreach ($request->file('imagenes') as $imagen) {

                    $nombre = uniqid() . '.' . $imagen->extension();

                    $imagen->move(
                        public_path('imagenes/incorporaciones'),
                        $nombre
                    );

                    IncorporacionImagen::create([
                        'incorporacion_id' => $incorporacion->id,
                        'ruta' => 'imagenes/incorporaciones/' . $nombre
                    ]);
                }
            }

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

    public function obtener(Request $request)
    {
        $inc = Incorporacion::with('imagenes')->find($request->id);

        if (!$inc) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No encontrado'
            ]);
        }

        return response()->json([
            'estado' => true,
            'data' => $inc
        ]);
    }
    public function convertirProducto(Request $request)
    {
        DB::beginTransaction();

        try {
            $incorporacion = Incorporacion::find($request->id);
            if (!$incorporacion) {
                throw new \Exception("Incorporación no encontrada");
            }
            if ($incorporacion->producto_id != null) {
                throw new \Exception("Ya fue convertida");
            }
            $usuario = Auth::user();
            $producto = new Producto();
            $producto->usuario_creador_id = $usuario->id;
            $producto->nombre = $incorporacion->nombre_producto;
            $producto->descripcion = $incorporacion->descripcion_producto;
            $producto->medidas = $incorporacion->medidas;
            $producto->estado = 1;
            $producto->save();

            $orden = 1;

            foreach ($incorporacion->imagenes as $img) {

                $origen = public_path($img->ruta);

                if (file_exists($origen)) {

                    $nuevoNombre = time() . '_' . uniqid() . '.' . pathinfo($origen, PATHINFO_EXTENSION);

                    copy(
                        $origen,
                        public_path('imagenes/productos/' . $nuevoNombre)
                    );

                    ProductoImagen::create([
                        'usuario_creador_id' => $usuario->id,
                        'producto_id' => $producto->id,
                        'imagen' => $nuevoNombre,
                        'orden' => $orden++,
                        'estado' => 1
                    ]);
                }
            }

            $incorporacion->producto_id = $producto->id;
            $incorporacion->save();
            $incorporacion->delete();
            DB::commit();
            return response()->json([
                'estado' => true,
                'mensaje' => 'Producto creado correctamente'
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