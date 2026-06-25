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

            $ultimo = Movimiento::where('tipo', 'INGRESO')
                ->max('compra_ingreso');

            $compra_ingreso = $ultimo ? $ultimo + 1 : 1;

            $request->validate([
                'idProd' => 'required',
                'idSuc' => 'required',
                'cantidad' => 'required|numeric|min:1',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',

            ]);

            $usuario = Auth::user();

            Movimiento::create([
                'usuario_creador_id' => $usuario->id,
                'producto_id' => $producto_id,
                'sucursal_id' => $sucursal_id,
                'tipo' => 'INGRESO',
                'cantidad' => $request->cantidad,
                'precio_compra' => $request->precio_compra,
                'precio_venta' => $request->precio_venta,
                'compra_ingreso' => $compra_ingreso,
                'fecha' => now(),
                'descripcion' => $request->descripcion,
                'estado' => 'INGRESO'
            ]);

            if ($request->precio_compra || $request->precio_venta) {
                $producto = Producto::find($producto_id);
                $producto->precio_compra = $request->precio_compra;
                $producto->precio_venta = $request->precio_venta;
                $producto->save();
            }

            return Respuesta::success(
                null,
                "Ingreso registrado correctamente"
            );
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

            DB::beginTransaction();

            try {

                $producto = Producto::find($producto_id);

                if (!$producto) {
                    throw new \Exception("Producto no encontrado");
                }

                $stock = $this->obtenerStock(
                    $producto_id,
                    $sucursal_id
                );

                if ($stock < $request->cantidad) {
                    throw new \Exception(
                        "La salida es mayor al stock disponible"
                    );
                }

                $usuario = Auth::user();

                Movimiento::create([
                    'usuario_creador_id' => $usuario->id,
                    'producto_id' => $producto_id,
                    'sucursal_id' => $sucursal_id,
                    'tipo' => 'SALIDA',
                    'cantidad' => $request->cantidad,
                    'motivo' => $request->motivo,
                    'fecha' => now(),
                    'descripcion' => $request->descripcion,
                    'estado' => 'SALIDA'
                ]);

                DB::commit();

                return Respuesta::success(
                    null,
                    "Salida registrada correctamente"
                );

            } catch (\Exception $e) {

                DB::rollBack();

                return Respuesta::error(
                    null,
                    $e->getMessage()
                );
            }
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


    public function guardarTransferencia(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'producto_id' => 'required',
                'sucursal_origen_id' => 'required',
                'sucursal_destino_id' => 'required',
                'cantidad' => 'required|numeric|min:1',
            ]);

            $producto = Producto::find($request->producto_id);

            if (!$producto) {
                throw new \Exception('Producto no encontrado');
            }

            $stock = $this->obtenerStock(
                $request->producto_id,
                $request->sucursal_origen_id
            );

            if ($stock < $request->cantidad) {
                throw new \Exception('Stock insuficiente');
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
                'estado' => 'SALIDA'
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
                'estado' => 'INGRESO'
            ]);

            DB::commit();

            return Respuesta::success(
                null,
                'Transferencia realizada'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return Respuesta::error(
                null,
                $e->getMessage()
            );
        }
    }

    public function obtenerStock($productoId, $sucursalId)
    {
        $ingresos = Movimiento::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->whereIn('tipo', [
                'INGRESO',
                'DEVOLUCION',
                'TRANSFERENCIA_INGRESO',
                'ANULACION_VENTA'
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



}