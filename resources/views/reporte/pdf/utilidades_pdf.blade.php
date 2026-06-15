<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>UTILIDADES</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        .titulo {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .producto {
            font-weight: bold;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="titulo">
        REPORTE DE UTILIDADES
    </div>
    <div class="subtitulo">
        Fecha generación:
        {{ now()->format('d/m/Y H:i') }}
    </div>
    @php
        $totalUtilidad = 0;
    @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>P. Compra</th>
                <th>P. Venta</th>
                <th>Utilidad Unit.</th>
                <th>Utilidad Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $index => $detalle)
                    @php
                        $utilidadUnitaria =
                            $detalle->precio_unitario -
                            $detalle->precio_compra;
                        $utilidad =
                            $utilidadUnitaria *
                            $detalle->cantidad;
                        $totalUtilidad += $utilidad;
                    @endphp
                    <tr>
                        <td>
                            {{ $index + 1 }}
                        </td>
                        <td>
                            {{ $detalle->producto->nombre ?? '' }}
                        </td>
                        <td>
                            {{ $detalle->cantidad }}
                        </td>
                        <td class="text-right">
                            {{
                number_format(
                    $detalle->precio_compra,
                    2
                )
                                        }}
                        </td>
                        <td class="text-right">
                            {{
                number_format(
                    $detalle->precio_unitario,
                    2
                )
                                        }}
                        </td>
                        <td class="text-right">
                            {{
                number_format(
                    $utilidadUnitaria,
                    2
                )
                                        }}
                        </td>
                        <td class="text-right">

                            {{
                number_format(
                    $utilidad,
                    2
                )
                                        }}
                        </td>
                    </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="6" class="text-right">
                    TOTAL UTILIDAD
                </td>
                <td class="text-right">

                    {{
    number_format(
        $totalUtilidad,
        2
    )
                    }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>