<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Devoluciòn</title>
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

        .devolucion {
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
@php
    $venta = $devolucion->venta;
@endphp

<body>
    <div class="devolucion">
        <div class="titulo">
            NOTA DE DEVOLUCIÓN N°
            {{ sprintf('%06d', $devolucion->id) }}
        </div>
        <table id="tabla">
            <tr>
                <td><strong>Tipo:</strong></td>
                <td>{{ $devolucion->tipo }}</td>

                <td><strong>Fecha:</strong></td>
                <td>
                    {{ date(
    'd/m/Y',
    strtotime($devolucion->created_at)
) }}
                </td>
            </tr>

            <tr>
                <td><strong>Motivo:</strong></td>
                <td colspan="3">
                    {{ $devolucion->motivo }}
                </td>
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
                    <td colspan="3">
                        <strong>TOTAL DEVOLUCIÓN</strong>
                    </td>
                    <td>
                        {{ number_format($devolucion->total, 2) }}
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <strong>TIPO</strong>
                    </td>
                    <td>
                        {{ $devolucion->tipo }}
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <strong>ESTADO</strong>
                    </td>
                    <td>
                        {{ $devolucion->estado }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>