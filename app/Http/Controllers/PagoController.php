<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\SubCategoria;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use App\Utils\Respuesta;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PagoController extends Controller
{
    public function listado(Request $request)
    {
        $usuario = Auth::user();
        $sucursal = $usuario->sucursal;
        $fechaIni = date('Y-m-d');
        $fechaFin = date('Y-m-d');
        $usuarios = User::where('id', $usuario->id)->get();
        $sucursales = Sucursal::where('id', $sucursal->id)->get();
        $categoriasIngreso = Categoria::whereNull('parent_id')->get();
        $categoriasSalida = Categoria::whereNull('parent_id')->get();
        $subCategorias = Categoria::whereNotNull('parent_id')->get();
        return view('pago.listado')->with(compact('sucursales', 'fechaIni', 'fechaFin', 'usuarios', 'usuario', 'categoriasIngreso', 'categoriasSalida', 'subCategorias'));
    }

    public function ajaxListado(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['estado' => false]);
        }
        $sucursal_id = $request->input('sucursal_id');
        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');
        $usuario_id = $request->input('usuario_busqueda_id');
        $baseQuery = Pago::query()
            ->with(['venta.detalles.producto']); // IMPORTANTE

        if ($sucursal_id) {
            $baseQuery->where('sucursal_id', $sucursal_id);
        }
        if ($fecha_ini && $fecha_fin) {
            $baseQuery->whereBetween('fecha', [
                $fecha_ini . ' 00:00:00',
                $fecha_fin . ' 23:59:59'
            ]);
        }
        if ($usuario_id) {
            $baseQuery->where('usuario_creador_id', $usuario_id);
        }
        $todos = (clone $baseQuery)
            ->orderBy('id', 'desc')
            ->get();
        $repuestos = (clone $baseQuery)
            ->whereHas('venta.detalles.producto', function ($q) {
                $q->where('tipo_producto', 'REPUESTO');
            })
            ->orderBy('id', 'desc')
            ->get();
        $lubricantes = (clone $baseQuery)
            ->whereHas('venta.detalles.producto', function ($q) {
                $q->where('tipo_producto', 'LUBRICANTE');
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'estado' => true,
            'data' => [
                'todos' => view('pago.ajaxListado', ['pagos' => $todos])->render(),
                'repuestos' => view('pago.ajaxListado', ['pagos' => $repuestos])->render(),
                'lubricantes' => view('pago.ajaxListado', ['pagos' => $lubricantes])->render(),
            ]
        ]);
    }

    public function listadoDeuda()
    {
        $usuario = Auth::user();
        $sucursal = $usuario->sucursal;
        return view('pago.listadoDeuda')->with(compact('usuario'));
    }

    public function ajaxListadoDeuda(Request $request)
    {
        if ($request->ajax()) {
            $ventas = Venta::with(['cliente', 'sucursal'])->where('estado_pago', 'DEUDA')->orderBy('id', 'desc')->get();
            $valores = [
                'listado' => view('pago.ajaxListadoDeuda')->with(compact('ventas'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function ajaxFormPagoDeuda(Request $request)
    {
        if ($request->ajax()) {
            $venta_id = $request->input('venta_id');

            $venta = Venta::with(['cliente', 'sucursal'])->where('id', $venta_id)->first();
            $pagos = pago::where('venta_id', $venta_id)
                ->where('estado', 'INGRESO')
                ->get();
            $pagado = pago::where('venta_id', $venta_id)
                ->where('estado', 'INGRESO')
                ->sum('monto');

            $valores = [
                'formulario' => view('pago.ajaxFormPagoDeuda')->with(compact('venta', 'pagos', 'pagado'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarPagoDeuda(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'venta_id' => 'required',
                'tipo_pago' => 'required',
                'importe_pago' => 'required',
                'saldo' => 'required',
            ]);
            $venta_id = $request->input('venta_id');
            $tipo_pago = $request->input('tipo_pago');
            $importe_pago = $request->input('importe_pago');
            $saldo = $request->input('saldo');
            $usuario = Auth::user();
            $sucursal = $usuario->sucursal;

            if ($importe_pago > 0 && $importe_pago <= $saldo) {
                $nuevo = new pago();
                $nuevo->usuario_creador_id = $usuario->id;
                $nuevo->venta_id = $venta_id;
                $nuevo->sucursal_id = $sucursal->id;
                $nuevo->monto = $importe_pago;
                $nuevo->cambio = 0;
                $nuevo->fecha = date('Y-m-d H:i:s');
                $nuevo->descripcion = 'VENTA';
                $nuevo->tipo_pago = $tipo_pago;
                $nuevo->estado = 'INGRESO';
                $nuevo->save();

                if (($saldo - $importe_pago) == 0) {
                    $venta = Venta::find($venta_id);
                    $venta->estado_pago = 'PAGADO';
                    $venta->save();
                }

                $data = Respuesta::success(null, "Datos obtenidos correctamente");
            } else {
                $data = Respuesta::error(null, "El importe debe ser mayor a 0 y menor al saldo.");
            }
        } else {
            $data = Respuesta::error(null, "Error en registro de datos.");
        }
        return $data;
    }

    public function guardarTipoIngresoSalida(Request $request)
    {
        if ($request->ajax()) {
            $usuario = Auth::user();
            $monto = $request->input('monto');
            $descripcion = $request->input('descripcion');
            $tipo = $request->input('tipo');
            $subcategoria_id = $request->input('subcategoria_id');
            $sucursal = $usuario->sucursal;
            $pago = new pago();
            $pago->usuario_creador_id = $usuario->id;
            $pago->sucursal_id = $sucursal->id;
            $pago->monto = $monto;
            $pago->fecha = date('Y-m-d H:i:s');
            $pago->descripcion = $descripcion;
            $pago->tipo_pago = 'EFECTIVO';
            $pago->estado = $tipo;
            $pago->sub_categoria_id = $subcategoria_id;
            $pago->save();

            $data = Respuesta::success(null, "Datos registrados correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function formularioDecuentoAdicional(Request $request)
    {
        if ($request->ajax()) {
            $venta_id = $request->input('venta_id');
            $venta = Venta::find($venta_id);
            $pagado = pago::where('venta_id', $venta_id)
                ->where('estado', 'INGRESO')
                ->sum('monto');
            $valores = [
                'formulario' => view('pago.formularioDecuentoAdicional')->with(compact('venta', 'pagado'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarDescuentoAdicional(Request $request)
    {
        if ($request->ajax()) {
            $usuario = Auth::user();
            $venta_id = $request->input('venta_id');
            $descuento_adicional = $request->input('descuento_adicional');
            $descripcion_descuento_adicional = $request->input('descripcion_descuento_adicional');
            $venta = Venta::find($venta_id);
            $venta->usuario_modificador_id = $usuario->id;
            $venta->descuento_adicional = $descuento_adicional;
            $venta->descripcion = $descripcion_descuento_adicional;
            $venta->save();
            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function generaExcelPago(Request $request)
    {
        if ($request->ajax()) {
            $sucursal_id = $request->input('sucursal_id');
            $fecha_ini = $request->input('fecha_ini');
            $fecha_fin = $request->input('fecha_fin');
            $usuario_id = $request->input('usuario_busqueda_id');

            $query = Pago::select();

            if ($sucursal_id != null) {
                $query->where('sucursal_id', $sucursal_id);
            }

            if ($fecha_ini != null && $fecha_fin != null) {
                $query->where('fecha', '>=', $fecha_ini . ' 00:00:00')
                    ->where('fecha', '<=', $fecha_fin . ' 23:59:59');
            }

            if ($usuario_id != null) {
                $query->where('usuario_creador_id', $usuario_id);
            }

            $pagos = $query->orderBy('id', 'desc')->get();
            $fileName = 'Pagos.xlsx';
            $libro = new Spreadsheet();
            $hoja = $libro->getActiveSheet();
            $hoja->getColumnDimension('A')->setWidth(15); // N°
            $hoja->getColumnDimension('B')->setWidth(25); // SUCURSAL
            $hoja->getColumnDimension('C')->setWidth(25); // FECHA
            $hoja->getColumnDimension('D')->setWidth(15); // DESCRIPCION
            $hoja->getColumnDimension('E')->setWidth(20); // CATEGORIA PAGO
            $hoja->getColumnDimension('F')->setWidth(15); // SUB CATEGORIA/REC
            $hoja->getColumnDimension('G')->setWidth(20); // TIPO PAGO EFECTIVO
            $hoja->getColumnDimension('H')->setWidth(20); // FAC/REC DEPOSITO
            $hoja->getColumnDimension('I')->setWidth(20); // MONTO EFECTIVO
            $hoja->getColumnDimension('J')->setWidth(20); // MONTO DEPOSITO
            $hoja->getColumnDimension('K')->setWidth(20); // ESTADO
            $hoja->getColumnDimension('L')->setWidth(20); // USUARIO
            $hoja->getColumnDimension('M')->setWidth(20); // VIGENCIA
            $hoja->setCellValue('A1', "REPORTE CENTRALIZADO DE PAGOS");
            $hoja->setCellValue('A2', "LISTADO DE PAGOS");
            $hoja->setCellValue('A3', "FECHA DE REPORTE: " . date('d/m/Y H:i:s'));

            $hoja->setCellValue('A4', "N°");
            $hoja->setCellValue('B4', "SUCURSAL");
            $hoja->setCellValue('C4', "FECHA");
            $hoja->setCellValue('D4', "DESCRIPCION");
            $hoja->setCellValue('E4', "CATEGORIA");
            $hoja->setCellValue('F4', "SUB CATEGORIA");
            $hoja->setCellValue('G4', "TIPO PAGO");
            $hoja->setCellValue('H4', "FAC/REC");
            $hoja->setCellValue('I4', "MONTO EFECTIVO");
            $hoja->setCellValue('J4', "MONTO DEPOSITO");
            $hoja->setCellValue('K4', "ESTADO");
            $hoja->setCellValue('L4', "USUARIO");
            $hoja->setCellValue('M4', "VIGENCIA");

            $encabezadoStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];

            $hoja->mergeCells('A1:M1');
            $hoja->mergeCells('A2:M2');
            $hoja->mergeCells('A3:M3');

            $hoja->getStyle('A1')->applyFromArray($encabezadoStyle);
            $hoja->getStyle('A2')->applyFromArray($encabezadoStyle);
            $hoja->getStyle('A3')->applyFromArray($encabezadoStyle);

            // Aplicar márgenes y formato a los encabezados
            $encabezadoStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFFFE0B2', // Color de fondo
                    ],
                ],
            ];
            $hoja->getStyle('A4:M4')->applyFromArray($encabezadoStyle);

            $contadorInicio = 5;
            $m = 0;
            $m1 = 1;
            foreach ($pagos as $key => $pago) {

                $hoja->setCellValue('A' . $contadorInicio, ($key + 1));
                $hoja->setCellValue('B' . $contadorInicio, $pago->sucursal?->nombre);
                $hoja->setCellValue('C' . $contadorInicio, $pago->fecha);
                $hoja->setCellValue('D' . $contadorInicio, $pago->descripcion);

                $hoja->setCellValue('E' . $contadorInicio, $pago->subCategoria?->Categoria?->nombre);
                $hoja->setCellValue('F' . $contadorInicio, $pago->subCategoria?->nombre);

                $hoja->setCellValue('G' . $contadorInicio, $pago->tipo_pago);
                $hoja->setCellValue('H' . $contadorInicio, $pago->venta ? ($pago->venta->numero_venta) : "");

                if ($pago->estado === 'INGRESO')
                    $m = ($pago->tipo_pago === 'EFECTIVO') ? $pago->monto : 0;
                elseif ($pago->estado === 'SALIDA')
                    $m = ($pago->tipo_pago === 'EFECTIVO') ? $pago->monto : 0;

                $hoja->setCellValue('I' . $contadorInicio, $m);

                if ($pago->estado === 'INGRESO')
                    $m1 = ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR') ? $pago->monto : 0;
                elseif ($pago->estado === 'SALIDA')
                    $m1 = ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR') ? $pago->monto : 0;

                $hoja->setCellValue('J' . $contadorInicio, $m1);

                $hoja->setCellValue('K' . $contadorInicio, $pago->estado);
                $hoja->setCellValue('L' . $contadorInicio, $pago->usuario->name);
                $hoja->setCellValue('M' . $contadorInicio, is_null($pago->fecha_anulacion) ? 'Vigente' : 'Anulado');

                $contadorInicio++;
            }

            $hoja->getStyle('A5:K' . ($contadorInicio - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($libro);
            $writer->save('php://output');
            exit;

        } else {
            $data = Respuesta::error(null, "Error en registro de datos.");
        }
        return $data;
    }

    public function comprobantePago(Request $request, $pago_id)
    {

        $pago = Pago::with([
            'usuario',
            'sucursal',
            'venta.pagos',
            'categoria.parent'
        ])->findOrFail($pago_id);

        $html = View::make('pago.pdf.comprobantePago', compact(['pago']))->render();
        $dompdf = new Dompdf();
        $dompdf->setPaper(array(0, 0, 300.00, 504.00), 'landscape');//cambio orientacion de la hoja
        $dompdf->loadHtml($html);
        $dompdf->render();

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename=Cotizacion.pdf');

    }
}
