<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\User;
use App\Models\Proveedor;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    private function obtenerStock($productoId)
    {
        return Movimiento::where('producto_id', $productoId)
            ->selectRaw("
            COALESCE(SUM(
                CASE
                    WHEN tipo = 'INGRESO' THEN cantidad
                    WHEN tipo = 'VENTA' THEN -cantidad
                    WHEN tipo = 'DEVOLUCION' THEN cantidad
                    ELSE 0
                END
            ), 0) as stock
        ")
            ->value('stock');
    }

    public function index()
    {
        $totalProductos = Producto::count();
        $ventasHoy = Venta::whereDate(
            'created_at',
            now()
        )->sum('total');

        $totalUsuarios = User::count();

        $totalProveedores = Proveedor::count();

        $productosStockBajo = Producto::whereColumn(
            'stock_actual',
            '<=',
            'stock_minimo'
        )->count();
        // $utilidades = Producto::sum(
        //     DB::raw('precio_venta - precio_compra')
        // );

        $utilidades = DB::table('venta_detalle')
            ->join('ventas', 'ventas.id', '=', 'venta_detalle.venta_id')
            ->whereDate('ventas.created_at', now())
            ->selectRaw('
        COALESCE(
            SUM(
                (
                    venta_detalle.precio_unitario
                    -
                    venta_detalle.precio_compra
                )
                *
                (
                    venta_detalle.cantidad
                    - COALESCE(venta_detalle.cantidad_devuelta, 0)
                )
            ),
            0
        ) as utilidad
    ')
            ->value('utilidad');

        $stockBajo = Producto::whereColumn(
            'stock_actual',
            '<=',
            'stock_minimo'
        )
            ->take(10)
            ->get();

        // $ultimosProductos = Producto::get();

        // $ultimosProductos = $ultimosProductos->map(function ($producto) {

        //     $stock = $this->obtenerStock(
        //         $producto->id,
        //         $producto->sucursal_id
        //     );

        //     $producto->stock_actual = max(0, $stock);

        //     return $producto;

        // })->filter(function ($producto) {

        //     return $producto->stock_actual <= $producto->stock_minimo;

        // })->sortBy('stock_actual')
        // ->take(10)
        // ->values();

        $productos = Producto::get()
            ->map(function ($producto) {

                $stock = $this->obtenerStock($producto->id);

                $producto->stock_actual_calculado = max(0, $stock);

                return $producto;

            })
            ->filter(function ($producto) {

                return $producto->stock_actual_calculado <= $producto->stock_minimo;

            })
            ->sortBy('stock_actual_calculado')
            ->take(10)
            ->values();


        $ultimasVentas = Venta::with('cliente')
            ->latest()
            ->take(10)
            ->get();
        $ventasMensuales = Venta::selectRaw('
                MONTH(created_at) as mes_numero,
                SUM(total) as total
            ')
            ->groupBy('mes_numero')
            ->orderBy('mes_numero')
            ->get()
            ->map(function ($item) {

                $meses = [
                    1 => 'Enero',
                    2 => 'Febrero',
                    3 => 'Marzo',
                    4 => 'Abril',
                    5 => 'Mayo',
                    6 => 'Junio',
                    7 => 'Julio',
                    8 => 'Agosto',
                    9 => 'Septiembre',
                    10 => 'Octubre',
                    11 => 'Noviembre',
                    12 => 'Diciembre'
                ];

                return (object) [
                    'mes' => $meses[$item->mes_numero],
                    'total' => $item->total
                ];
            });



        return view('home.inicio', compact(
            'totalProductos',
            'ventasHoy',
            'totalUsuarios',
            'totalProveedores',
            'productosStockBajo',
            'utilidades',
            'stockBajo',
            'productos',
            'ultimasVentas',
            'ventasMensuales'
        ));
    }
}