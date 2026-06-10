<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function listado()
    {
        $sucursales = Sucursal::all();
        return view('movimiento.listado')->with(compact('sucursales'));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $productoId = $request->productoId;
            $sucursales = Sucursal::with('movimientos')->get();
            $valores = [
                'stock' => view('movimiento.ajaxListado')
                    ->with(compact(
                        'sucursales',
                        'productoId'
                    ))
                    ->render()
            ];

            return Respuesta::success($valores, "Datos obtenidos correctamente");
        }
        return Respuesta::error(null, "Error");
    }
    public function guardarIngreso(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->idProd;
            $sucursal_id = $request->idSuc;
            $request->validate([
                'idProd' => 'required',
                'idSuc' => 'required',
                'cantidad' => 'required|numeric|min:1',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'compra_ingreso' => 'required|numeric|min:0',

            ]);
            $usuario = Auth::user();
            $movimiento = new Movimiento();
            $movimiento->usuario_creador_id = $usuario->id;
            $movimiento->producto_id = $producto_id;
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->tipo = 'INGRESO';
            $movimiento->cantidad = $request->cantidad;
            $movimiento->precio_compra = $request->precio_compra;
            $movimiento->precio_venta = $request->precio_venta;
            $movimiento->compra_ingreso = $request->compra_ingreso;
            $movimiento->fecha = now();
            $movimiento->descripcion = $request->descripcion;
            $movimiento->estado = 1;
            $movimiento->save();
            $producto = Producto::find($producto_id);
            $producto->precio_compra = $request->precio_compra;
            $producto->precio_venta = $request->precio_venta;
            $producto->compra_ingreso = $request->compra_ingreso;

            $producto->save();
            return Respuesta::success(null, "Ingreso registrado correctamente");
        }
        return Respuesta::error(null, "Error");
    }

    public function guardarSalida(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->idProds;
            $sucursal_id = $request->idSucs;
            $request->validate([
                'idProds' => 'required',
                'idSucs' => 'required',
                'cantidad' => 'required|numeric|min:1',
                'motivo' => 'required',
            ]);

            $producto = Producto::find($producto_id);
            $stockActual = $this->obtenerStock(
                $producto_id,
                $sucursal_id
            );
            if ($request->cantidad > $stockActual) {
                return Respuesta::error(
                    null,
                    "La salida es mayor al stock disponible"
                );
            }
            $usuario = Auth::user();
            $movimiento = new Movimiento();
            $movimiento->usuario_creador_id = $usuario->id;
            $movimiento->producto_id = $producto_id;
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->tipo = 'SALIDA';
            $movimiento->cantidad = $request->cantidad;
            $movimiento->motivo = $request->motivo;
            $movimiento->fecha = now();
            $movimiento->descripcion = $request->descripcion;
            $movimiento->estado = 1;
            $movimiento->save();
            $producto->save();
            return Respuesta::success(null, "Salida registrada correctamente");
        }
        return Respuesta::error(null, "Error");
    }

    public function sacarTipoIngreso(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->input('producto');
            $sucursal_id = $request->input('sucursal');
            $movimientos = Movimiento::select(
                'id',
                'compra_ingreso',
                'fecha',
                'cantidad'
            )
                ->where('sucursal_id', $sucursal_id)
                ->where('producto_id', $producto_id)
                ->where('tipo', 'INGRESO')
                ->get();

            $valores = [
                'select' => $movimientos
            ];
            return Respuesta::success(
                $valores,
                "Datos obtenidos correctamente"
            );
        }
        return Respuesta::error(
            null,
            "Error al obtener los datos"
        );
    }

    public function eliminarMovimiento(Request $request)
    {
        if ($request->ajax()) {
            $movimiento = Movimiento::find($request->movimiento_id);
            $usuario = Auth::user();
            $movimiento->usuario_eliminador_id = $usuario->id;
            $movimiento->deleted_at = now();
            $movimiento->save();
            return Respuesta::success(null, "Movimiento eliminado");
        }
        return Respuesta::error(null, "Error");
    }


    public function obtenerStock($productoId, $sucursalId)
    {
        $ingresos = Movimiento::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->whereIn('tipo', [
                'INGRESO',
                'DEVOLUCION',
                'TRANSFERENCIA_INGRESO'
            ])
            ->sum('cantidad');

        $salidas = Movimiento::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->whereIn('tipo', [
                'SALIDA',
                'VENTA',
                'TRANSFERENCIA_SALIDA'
            ])
            ->sum('cantidad');

        return $ingresos - $salidas;
    }

    public function guardarTransferencia(Request $request)
    {
        $request->validate([
            'producto_id' => 'required',
            'sucursal_origen_id' => 'required',
            'sucursal_destino_id' => 'required',
            'cantidad' => 'required|numeric|min:1',
        ]);

        $stock = $this->obtenerStock(
            $request->producto_id,
            $request->sucursal_origen_id
        );

        if ($request->cantidad > $stock) {

            return Respuesta::error(
                null,
                'Stock insuficiente'
            );
        }

        $usuario = Auth::user();
        Movimiento::create([
            'usuario_creador_id' => $usuario->id,
            'producto_id' => $request->producto_id,
            'sucursal_id' => $request->sucursal_origen_id,
            'tipo' => 'TRANSFERENCIA_SALIDA',
            'cantidad' => $request->cantidad,
            'fecha' => now(),
            'descripcion' =>
                'Transferencia a sucursal ' .
                $request->sucursal_destino_id,
            'estado' => 1
        ]);

        Movimiento::create([
            'usuario_creador_id' => $usuario->id,
            'producto_id' => $request->producto_id,
            'sucursal_id' => $request->sucursal_destino_id,
            'tipo' => 'TRANSFERENCIA_INGRESO',
            'cantidad' => $request->cantidad,
            'fecha' => now(),
            'descripcion' =>
                'Transferencia desde sucursal ' .
                $request->sucursal_origen_id,

            'estado' => 1
        ]);

        return Respuesta::success(
            null,
            'Transferencia realizada'
        );
    }



}