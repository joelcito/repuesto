<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>INVENTARIOS</title>

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
        REPORTE DE INVENTARIOS
    </div>

    <div class="subtitulo">
        Fecha generación:
        {{ now()->format('d/m/Y H:i') }}
    </div>

    @php
        $totalInventario = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Stock</th>
                <th>P. Compra</th>
                <th>P. Venta</th>
                <th>Valor Inventario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $index => $producto)
                    @php
                        $valorInventario =
                            $producto->stock_actual *
                            $producto->precio_compra;
                        $totalInventario += $valorInventario;
                    @endphp
                    <tr>
                        <td>
                            {{ $index + 1 }}
                        </td>
                        <td>
                            {{ $producto->codigo_interno }}
                        </td>
                        <td>
                            {{ $producto->nombre }}
                        </td>
                        <td>
                            {{ $producto->stock_actual }}
                        </td>
                        <td class="text-right">
                            {{
                number_format(
                    $producto->precio_compra,
                    2
                )
                                        }}
                        </td>
                        <td class="text-right">
                            {{
                number_format(
                    $producto->precio_venta,
                    2
                )
                                        }}
                        </td>
                        <td class="text-right">
                            {{
                number_format(
                    $valorInventario,
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
                    TOTAL INVENTARIO
                </td>
                <td class="text-right">
                    {{
    number_format(
        $totalInventario,
        2
    )
                    }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>