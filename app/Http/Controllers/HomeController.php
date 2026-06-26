<?php

namespace App\Http\Controllers;

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



        $utilidades = Producto::sum(
            DB::raw('precio_venta - precio_compra')
        );



        $stockBajo = Producto::whereColumn(
            'stock_actual',
            '<=',
            'stock_minimo'
        )
            ->take(10)
            ->get();



        $ultimosProductos = Producto::latest()
            ->take(10)
            ->get();



        $ultimasVentas = Venta::latest()
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
            'ultimosProductos',
            'ultimasVentas',
            'ventasMensuales'
        ));
    }
}