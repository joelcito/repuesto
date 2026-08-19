<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>VENTAS</title>

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
        REPORTE DE VENTAS
    </div>

    @php
        $totalVentas = 0;
    @endphp

    <table>

        <thead>

            <tr>

                <th>#</th>
                <th>Factura</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Total</th>
                <th>Método Pago</th>
                <th>Fecha</th>

            </tr>

        </thead>

        <tbody>
            @foreach($ventas as $index => $venta)
                @php
                    $totalVentas += $venta->total;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $venta->numero_factura }}</td>
                    <td>
                        {{ optional($venta->cliente)->name }}
                    </td>
                    <td>
                        {{ optional($venta->usuario)->name }}
                    </td>
                    <td>
                        {{ number_format($venta->total, 2) }}
                    </td>
                    <td>
                        {{ $venta->metodo_pago }}
                    </td>
                    <td>
                        {{ $venta->created_at->format('Y-m-d H:i') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="4">
                    TOTAL VENTAS
                </td>
                <td>
                    {{ number_format($totalVentas, 2) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>