<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cotización</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .header img {
            width: 90px;
        }

        .header-text {
            flex-grow: 1;
            text-align: center;
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

        .table_firmas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            text-align: center;
        }

        .footer {
            margin-top: 5px;
            text-align: right;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 2px 0 1px 0;
            color: #34495e;
        }
    </style>
</head>

<body>
    <div style="position: relative; width: 100%; margin-bottom: 1px; height: 90px;">
        <div style="position: absolute; top: 0; left: 0;">
            <img src="" alt="Logo" width="90">
        </div>
        <div style="text-align: center;">
            <h2 style="margin: 0; font-size: 13pt;">
                COMPROBANTE DE {{ $pago->estado }}
            </h2>
            <p style="margin: 1pt 0; font-size: 11pt;">Fecha: {{ $pago->fecha }}</p>
        </div>
        <div style="text-align: end;">
            <p style="font-size: 10pt;">Vendedor: {{ $pago->usuario->name }}</p>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>SUCURSAL</th>
                <th>CATEGORIA</th>
                <th>SUB CATEGORIA</th>
                <th>FAC/OR REC.</th>
                <th>FECHA</th>
                <th>DESCRIPCION</th>
                <th>TIPO PAGO</th>
                <th>MONTO</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pago?->sucursal?->nombre }}</td>
                <td>{{ $pago->subCategoria?->Categoria?->nombre }}</td>
                <td>{{ $pago->subCategoria?->nombre }}</td>
                <td>{{ $pago->venta?->numero_factura }}</td>
                <td>{{ $pago->fecha }}</td>
                <td>{{ $pago->descripcion }}</td>
                <td>{{ $pago->tipo_pago }}</td>
                <td>{{ number_format($pago->monto, 2) }}</td>
                <td>{{ number_format(($pago->venta->total - $pago->venta->pagos->sum('monto')), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total: </strong>{{ number_format($pago->monto, 2) }} </p>
    </div>
    <br>
    <table class="table_firmas">
        <tr>
            <td>-------------------------------</td>
            <td>-------------------------------</td>
        </tr>
        <tr>
            <td>ENTREGE CONFORME</td>
            <td>RECIBI CONFORME</td>
        </tr>
    </table>
</body>

</html>