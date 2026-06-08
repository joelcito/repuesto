<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo</title>
    <style>
        @page {
            margin: 0;
            /* elimina márgenes del PDF */
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            /* opcional */
            font-size: 10px;
        }

        .recibo {
            width: 95%;
            height: 5.3in;
            /* un poco menos que 5.5 para evitar salto */
            border: 1px solid #000;
            padding: 20px;
            box-sizing: border-box;
            /* background-color:red; */
        }

        .titulo {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .contenido {
            font-size: 14px;
            line-height: 1.5;
            /* background-color:red; */
        }

        p {
            margin: 4px 0;
        }

        #tabla {
            width: 100%;
        }

        .text-left {
            text-align: left;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #444;
            padding: 2px;
            text-align: center;
        }

        .table th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="recibo">
        <div class="titulo"> NOTA DE VENTA N° {{ sprintf('%06d', $venta->numero_factura) }} </div>
        <table id="tabla">
            <tr>
                <td> <strong>Cliente:</strong> </td>
                <td class="text-left"> {{ $venta->cliente?->nombres }} {{ $venta->cliente?->ap_paterno }}
                    {{ $venta->cliente?->ap_materno }}
                </td>
                <td> <strong>Fecha:</strong> </td>
                <td class="text-left"> {{ date('d/m/Y', strtotime($venta->fecha)) }} </td>
            </tr>
            <tr>
                <td> <strong>Dirección:</strong> </td>
                <td class="text-left"> {{ $venta->cliente?->direccion }} </td>
                <td> <strong>Celular:</strong> </td>
                <td class="text-left"> {{ $venta->cliente?->celular }} </td>
            </tr>
        </table>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>CANTIDAD</th>
                    <th>TIPO PRECIO</th>
                    <th>SUBTOTAL</th>
                </tr>
            </thead>
            <tbody> @php $total = 0; @endphp @foreach ($venta->detalles as $detalle)
                @php $total += $detalle->subtotal; @endphp <tr>
                    <td> {{ $detalle->producto?->nombre }} </td>
                    <td> {{ $detalle->cantidad }} </td>
                    <td> {{ $detalle->tipo_precio }} </td>
                    <td> {{ number_format($detalle->subtotal, 2) }} </td>
            </tr> @endforeach </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"> <strong>SUBTOTAL</strong> </td>
                    <td> {{ number_format($venta->subtotal, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3"> <strong>DESCUENTO</strong> </td>
                    <td> {{ number_format($venta->descuento, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3"> <strong>TOTAL</strong> </td>
                    <td> {{ number_format($venta->total, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3"> <strong>PAGADO</strong> </td>
                    <td> {{ number_format($venta->pagos->sum('monto'), 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3"> <strong>SALDO</strong> </td>
                    <td> {{ number_format($venta->total - $venta->pagos->sum('monto'), 2) }} </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>