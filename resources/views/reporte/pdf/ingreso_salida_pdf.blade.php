<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ingreso salida</title>

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
        REPORTE INGRESOS Y SALIDAS INVENTARIOS
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>P. Compra</th>
                <th>P. Venta</th>
                <th>Motivo</th>
                <th>Tipo producto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $index => $movimiento)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $movimiento->fecha }}</td>
                    <td>
                        {{ $movimiento->producto->nombre ?? '' }}
                    </td>
                    <td>
                        {{ $movimiento->tipo }}
                    </td>
                    <td>
                        {{ $movimiento->cantidad }}
                    </td>
                    <td>
                        {{ number_format($movimiento->precio_compra, 2) }}
                    </td>
                    <td>
                        {{ number_format($movimiento->precio_venta, 2) }}
                    </td>
                    <td>
                        {{ $movimiento->motivo }}
                    </td>
                    <td>
                        {{ $movimiento->producto->tipo_producto ?? '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>