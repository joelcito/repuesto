<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Movimiento;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\UseUse;

class ReporteController extends Controller
{
    public function cajas(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.cajas')->with(compact('clientes'));
    }

    public function historial(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.historial_precios')->with(compact('clientes'));
    }

    public function ingresosalida(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.ingreso_salida')->with(compact('clientes'));
    }

    public function inventarios(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.inventarios')->with(compact('clientes'));
    }

    public function pagos(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.pagos')->with(compact('clientes'));
    }

    public function utilidades(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.utilidades')->with(compact('clientes'));
    }

    public function ventas(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.ventas')->with(compact('clientes'));
    }

    public function inventariosPdf(Request $request)
    {
        $usuario = Auth::user();

        $productos = Producto::with([
            'categoria',
            'proveedor',
            'sucursal'
        ])
            ->where('estado', 1)
            ->get();
        foreach ($productos as $producto) {
            $producto->stock_actual = $this->obtenerStock(
                $producto->id,
                $usuario->sucursal_id
            );
        }

        $pdf = Pdf::loadView(
            'reporte.pdf.inventarios_pdf',
            compact('productos')
        );
        return $pdf->stream('inventarios.pdf');
    }


    public function ventasPdf(Request $request)
    {
        $ventas = Venta::with([
            'cliente',
            'usuario',
            'pagos',
            'detalles' => function ($q) {
                $q->with([
                    'producto.marca',
                    'producto.unidad'
                ]);
            }
        ])
            ->whereBetween('fecha', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $ventas->each(function ($venta) {
            $venta->metodos_pago = $venta->pagos
                ->pluck('metodo')
                ->implode(', ');
        });
        $pdf = Pdf::loadView(
            'reporte.pdf.ventas_pdf',
            compact('ventas')
        );
        return $pdf->stream('ventas.pdf');
    }

    public function tiquetPdf(Request $request)
    {
        $ventas = Venta::with([
            'cliente',
            'usuario',
            'pagos',
            'detalles' => function ($q) {
                $q->with([
                    'producto.marca',
                    'producto.unidad'
                ]);
            }
        ])
            ->whereBetween('fecha', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $ventas->each(function ($venta) {
            $venta->metodos_pago = $venta->pagos
                ->pluck('metodo')
                ->implode(', ');
        });
        $pdf = Pdf::loadView(
            'reporte.pdf.tiquet_pdf',
            compact('ventas')
        );
        return $pdf->stream('tiquet.pdf');
    }

    public function cajasPdf(Request $request)
    {
        $cajas = Caja::with([
            'usuario',
            'sucursal'
        ])
            ->whereBetween('fecha_apertura', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $pdf = Pdf::loadView(
            'reporte.pdf.cajas_pdf',
            compact('cajas')
        );
        return $pdf->stream('cajas.pdf');
    }


    public function utilidadesPdf(Request $request)
    {
        $detalles = VentaDetalle::with([
            'producto',
            'venta'
        ])
            ->whereHas('venta', function ($q) use ($request) {

                $q->whereBetween('fecha', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);

            })
            ->get();

        $pdf = Pdf::loadView(
            'reporte.pdf.utilidades_pdf',
            compact('detalles')
        );
        return $pdf->stream('utilidades.pdf');
    }

    public function ingresoSalidaPdf(Request $request)
    {
        $movimientos = Movimiento::with([
            'producto',
            'sucursal'
        ])
            ->whereBetween('fecha', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->orderBy('fecha')
            ->get();

        $pdf = Pdf::loadView(
            'reporte.pdf.ingreso_salida_pdf',
            compact('movimientos')
        );
        return $pdf->stream('ingresos_salidas.pdf');
    }

    public function historialPdf(Request $request)
    {
        $movimientos = Movimiento::with('producto')
            ->whereNotNull('precio_compra')
            ->whereBetween('fecha', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $pdf = Pdf::loadView(
            'reporte.pdf.historial_precios_pdf',
            compact('movimientos')
        );
        return $pdf->stream('historial_precios.pdf');
    }

    public function pagosPdf(Request $request)
    {
        $pagos = Pago::with([
            'usuario',
            'venta',
            'caja',
            'sucursal',
            'categoria'
        ])
            ->whereBetween('fecha', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $pdf = Pdf::loadView(
            'reporte.pdf.pagos_pdf',
            compact('pagos')
        );

        return $pdf->stream('pagos.pdf');
    }

    private function obtenerStock($productoId, $sucursalId)
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