<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Movimiento;
use App\Models\MovimientoCaja;
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


        return view('reporte.inventarios')
            ->with(compact('clientes', 'sucursales'));
    }

    public function pagos(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        $sucursales = Sucursal::where('estado', 1)->get();
        return view('reporte.pagos')->with(compact('clientes'));
    }

    public function utilidades(Request $request)
    {
        $clientes = User::where('rol_id', 3)->get();
        return view('reporte.utilidades')->with(compact('clientes'));
    }

    public function ventas(Request $request)
    {
        return view('reporte.ventas');
    }

    public function inventariosPdf(Request $request)
    {
        $productos = Producto::with([
            'categoria',
            'proveedor'
        ])
            ->where('estado', 1)
            ->when(
                $request->tipo_producto !== 'TODOS',
                function ($query) use ($request) {
                    $query->where(
                        'tipo_producto',
                        $request->tipo_producto
                    );
                }
            )
            ->get();

        foreach ($productos as $producto) {

            $producto->stock_actual = max(
                0,
                $this->obtenerStock(
                    $producto->id,
                    $request->sucursal_id
                )
            );
        }

        $productos = $productos->filter(function ($producto) {
            return $producto->stock_actual > 0;
        });

        $sucursal = Sucursal::findOrFail($request->sucursal_id);

        $pdf = Pdf::loadView(
            'reporte.pdf.inventarios_pdf',
            compact(
                'productos',
                'sucursal'
            )
        );

        return $pdf->stream('inventarios.pdf');
    }


    public function ventasPdf(Request $request)
    {
        $ventas = Venta::with([
            'cliente',
            'usuario',
            'pagos',
            'detalles' => function ($q) use ($request) {

                $q->with([
                    'producto.marca',
                    'producto.unidad'
                ]);

                if ($request->tipo_producto !== 'TODOS') {
                    $q->whereHas('producto', function ($producto) use ($request) {
                        $producto->where(
                            'tipo_producto',
                            $request->tipo_producto
                        );
                    });
                }
            }
        ])
            ->whereBetween('fecha', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $ventas = $ventas->filter(function ($venta) {
            return $venta->detalles->count() > 0;
        });

        $ventas->each(function ($venta) {

            $venta->metodos_pago = $venta->pagos
                ->pluck('metodo')
                ->implode(', ');

            $venta->total_filtrado = $venta->detalles->sum('subtotal');
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

        foreach ($cajas as $caja) {



            $movimientos = MovimientoCaja::with([
                'venta.detalles.producto'
            ])
                ->where('caja_id', $caja->id)
                ->whereBetween('fecha', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ])
                ->get();

            $ingresos = 0;
            $egresos = 0;

            foreach ($movimientos as $movimiento) {



                if ($request->tipo_producto === 'TODOS') {

                    if ($movimiento->tipo === 'INGRESO') {
                        $ingresos += $movimiento->monto;
                    }

                    if ($movimiento->tipo === 'EGRESO') {
                        $egresos += $movimiento->monto;
                    }

                    continue;
                }


                if (!$movimiento->venta) {
                    continue;
                }

                $montoTipo = 0;

                foreach ($movimiento->venta->detalles as $detalle) {

                    if (
                        optional($detalle->producto)->tipo_producto
                        === $request->tipo_producto
                    ) {
                        $montoTipo += $detalle->subtotal;
                    }
                }



                if (
                    $movimiento->tipo === 'INGRESO' &&
                    $montoTipo > 0
                ) {
                    $ingresos += $montoTipo;
                }



                if ($movimiento->tipo === 'EGRESO') {
                    $egresos += $montoTipo;
                }
            }

            $caja->ingresos_filtrados = $ingresos;
            $caja->egresos_filtrados = $egresos;


            $caja->saldo_filtrado =
                $caja->monto_apertura
                + $ingresos
                - $egresos;
        }

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
            ->when(
                $request->tipo_producto !== 'TODOS',
                function ($query) use ($request) {

                    $query->whereHas('producto', function ($producto) use ($request) {

                        $producto->where(
                            'tipo_producto',
                            $request->tipo_producto
                        );

                    });

                }
            )
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
            ->when(
                $request->tipo_producto !== 'TODOS',
                function ($query) use ($request) {

                    $query->whereHas('producto', function ($producto) use ($request) {

                        $producto->where(
                            'tipo_producto',
                            $request->tipo_producto
                        );

                    });

                }
            )
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
            ->when(
                $request->tipo_producto !== 'TODOS',
                function ($query) use ($request) {

                    $query->whereHas('producto', function ($producto) use ($request) {

                        $producto->where(
                            'tipo_producto',
                            $request->tipo_producto
                        );

                    });

                }
            )
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
            ->when(
                $request->tipo_producto !== 'TODOS',
                function ($query) use ($request) {

                    $query->whereHas(
                        'venta.detalles.producto',
                        function ($producto) use ($request) {

                            $producto->where(
                                'tipo_producto',
                                $request->tipo_producto
                            );

                        }
                    );

                }
            )
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