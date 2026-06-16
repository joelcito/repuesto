<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>PAGOS</title>

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
        REPORTE DE PAGOS
    </div>
    </hr>
    <div class="subtitulo">
        Fecha generación:

        {{ now()->format('d/m/Y H:i') }}

    </div>
    @php
        $totalPagos = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Monto</th>
                <th>Cambio</th>
                <th>Tipo Pago</th>
                <th>Sucursal</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $index => $pago)
                    @php
                        $totalPagos += $pago->monto;
                    @endphp
                    <tr>
                        <td>
                            {{ $index + 1 }}
                        </td>
                        <td>
                            {{ $pago->fecha }}
                        </td>
                        <td>
                            {{
                optional(
                    optional($pago->venta)->cliente
                )->name
                                        }}

                        </td>
                        <td class="text-right">
                            {{
                number_format(
                    $pago->monto,
                    2
                )
                                        }}
                        </td>
                        <td class="text-right">

                            {{
                number_format(
                    $pago->cambio,
                    2
                )
                                        }}
                        </td>
                        <td>
                            {{ $pago->tipo_pago }}
                        </td>
                        <td>
                            {{ $pago->sucursal->nombre ?? '' }}
                        </td>
                        <td>
                            {{ $pago->usuario->name ?? '' }}
                        </td>
                    </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3" class="text-right">
                    TOTAL PAGOS
                </td>
                <td class="text-right">

                    {{
    number_format(
        $totalPagos,
        2
    )
                    }}
                </td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>