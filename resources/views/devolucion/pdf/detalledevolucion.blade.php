<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Devoluciòn</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font-size: 10px;
        }

        .devolucion {
            width: 95%;
            height: 5.3in;
            border: 1px solid #000;
            padding: 20px;
            box-sizing: border-box;
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
        <table class="table">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO</th>
                    <th>DESC.</th>
                    <th>PRECIO FINAL</th>
                    <th>SUBTOTAL</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($devolucion->detalles as $detalle)

                    @php
                        $descuentoUnitario = ($detalle->descuento ?? 0) / $detalle->cantidad;
                        $precioFinal = $detalle->precio_unitario - $descuentoUnitario;
                    @endphp

                    <tr>
                        <td>{{ $detalle->producto?->nombre }}</td>

                        <td class="text-center">
                            {{ $detalle->cantidad }}
                        </td>

                        <td class="text-end">
                            {{ number_format($detalle->precio_unitario, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($descuentoUnitario, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($precioFinal, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($detalle->subtotal, 2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

            <tfoot>

                <tr>
                    <td colspan="5">
                        <strong>TOTAL DEVOLUCIÓN</strong>
                    </td>

                    <td>
                        <strong>
                            {{ number_format($devolucion->total, 2) }}
                        </strong>
                    </td>
                </tr>

                <tr>
                    <td colspan="5">
                        <strong>TIPO</strong>
                    </td>

                    <td>{{ $devolucion->tipo }}</td>
                </tr>

                <tr>
                    <td colspan="5">
                        <strong>ESTADO</strong>
                    </td>

                    <td>{{ $devolucion->estado }}</td>
                </tr>

            </tfoot>

        </table>
    </div>
</body>

</html>