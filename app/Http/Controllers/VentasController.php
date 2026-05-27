<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Movimiento;
use App\Models\MovimientoCaja;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Utils\Respuesta;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentasController extends Controller
{
    public function listado()
    {
        $clientes = User::get();
        $productos = Producto::where('estado', 1)->get();
        $cajas = Caja::where('estado', 'ABIERTA')->get();
        return view(
            'venta.listado',
            compact(
                'clientes',
                'productos',
                'cajas'
            )
        );
    }

    public function ajaxListado()
    {
        $ventas = Venta::with([
            'cliente',
            'usuarioCreador'
        ])
            ->orderBy('id', 'desc')
            ->get();
        $listado = view(
            'venta.ajaxListado',
            compact('ventas')
        )->render();
        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }

    public function guardarVenta(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'caja_id' => 'required',
                'metodo_pago' => 'required',
                'productos' => 'required|array|min:1'
            ]);
            $usuario = Auth::user();
            $caja = Caja::where('id', $request->caja_id)
                ->where('estado', 'ABIERTA')
                ->first();
            if (!$caja) {
                throw new \Exception('La caja no está abierta');
            }
            $venta = new Venta();
            $venta->usuario_cliente_id = $request->cliente_id;
            $venta->usuario_venta_id = $usuario->id;
            $venta->caja_id = $request->caja_id;
            $venta->subtotal = 0;
            $venta->descuento = $request->descuento ?? 0;
            $venta->total = 0;
            $venta->metodo_pago = $request->metodo_pago;
            $venta->observacion = $request->observacion;
            $venta->estado = 'ACTIVO';
            $venta->usuario_creador_id = $usuario->id;
            $venta->save();
            $subtotalGeneral = 0;

            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);
                if (!$producto) {
                    throw new \Exception("Producto no encontrado");
                }
                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente de: " . $producto->nombre);
                }
                $precio = $item['precio'];
                $subtotal = $precio * $item['cantidad'];
                $detalle = new VentaDetalle();
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $producto->id;
                $detalle->cantidad = $item['cantidad'];
                $detalle->precio_unitario = $precio;
                $detalle->tipo_precio = $item['tipo_precio'];
                $detalle->subtotal = $subtotal;
                $detalle->estado = 'ACTIVO';
                $detalle->usuario_creador_id = $usuario->id;
                $detalle->save();
                // DESCONTAR STOCK
                $producto->stock_actual =
                    $producto->stock_actual - $item['cantidad'];
                $producto->save();

                // MOVIMIENTO STOCK
                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $producto->sucursal_id,
                    'tipo' => 'VENTA',
                    'cantidad' => $item['cantidad'],
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $precio,
                    'compra_ingreso' => null,
                    'motivo' => 'VENTA',
                    'fecha' => now(),
                    'descripcion' => 'VENTA #' . $venta->id,
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => $usuario->id
                ]);
                $subtotalGeneral += $subtotal;
            }

            $total = $subtotalGeneral - $venta->descuento;
            $venta->subtotal = $subtotalGeneral;
            $venta->total = $total;
            $venta->save();

            // MOVIMIENTO CAJA
            MovimientoCaja::create([
                'caja_id' => $venta->caja_id,
                'venta_id' => $venta->id,
                'tipo' => 'INGRESO',
                'metodo_pago' => $venta->metodo_pago,
                'monto' => $venta->total,
                'descripcion' => 'VENTA #' . $venta->id,
                'fecha' => now(),
                'estado' => 'ACTIVO',
                'usuario_creador_id' => $usuario->id
            ]);

            $caja->total_ingresos =
                $caja->total_ingresos + $venta->total;

            $caja->save();

            DB::commit();
            return response()->json([
                'estado' => true,
                'mensaje' => 'Venta registrada correctamente'
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

