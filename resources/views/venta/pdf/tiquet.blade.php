<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tiquet</title>
    <style>
        @page {
            margin: 5mm;
        }

        body {
            font-family: monospace;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .tiquet {
            width: 100%;
        }

        .titulo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .linea {
            text-align: center;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 2px 0;
            font-size: 11px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
        }

        .gracias {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="tiquet">
        <div class="titulo"> NOTA DE VENTA N° {{ sprintf('%06d', $venta->numero_factura) }} </div>

        <div class="linea">
            ------------------------------------------
        </div>

        <table>

            <tr>
                <td width="35%">Cliente:</td>
                <td>
                    {{ $venta->cliente?->nombres }}
                    {{ $venta->cliente?->ap_paterno }}
                    {{ $venta->cliente?->ap_materno }}
                </td>
            </tr>

            <tr>
                <td>Celular:</td>
                <td>{{ $venta->cliente?->celular }}</td>
            </tr>

            <tr>
                <td>C.I.:</td>
                <td>{{ $venta->cliente?->cedula }}</td>
            </tr>

            <tr>
                <td>NIT:</td>
                <td>{{ $venta->cliente?->nit }}</td>
            </tr>

            <tr>
                <td>Atendido:</td>
                <td>
                    {{ $venta->usuario?->nombres }}
                    {{ $venta->usuario?->ap_paterno }}
                </td>
            </tr>

            <tr>
                <td>Fecha y Hora:</td>
                <td>{{ date('d/m/Y H:i', strtotime($venta->created_at)) }}</td>
            </tr>

        </table>

        <div class="linea">
            --------------------------------------------
        </div>

        <table>

            <tr>
                <td width="12%"><strong>CANT</strong></td>
                <td width="48%"><strong>PRODUCTO</strong></td>
                <td width="20%" class="right"><strong>P.UNIT</strong></td>
                <td width="20%" class="right"><strong>SUBTOT.</strong></td>
            </tr>

        </table>

        <div class="linea">
            --------------------------------------------
        </div>

        @foreach($venta->detalles as $detalle)

            <div class="producto">

                <table>

                    <tr>

                        <td width="12%">
                            {{ $detalle->cantidad }}
                        </td>

                        <td colspan="3">
                            {{ $detalle->producto?->codigo_interno }}
                        </td>

                    </tr>

                    <tr>

                        <td></td>

                        <td colspan="3">
                            {{ $detalle->producto?->nombre }}
                        </td>

                    </tr>

                    <tr>

                        <td></td>

                        <td>
                            {{ $detalle->producto?->marca?->nombre }}
                        </td>

                        <td>
                            {{ $detalle->producto?->unidad?->nombre }}
                        </td>

                        <td></td>

                    </tr>

                    <tr>

                        <td></td>

                        <td></td>

                        <td class="right">
                            {{ number_format($detalle->precio_unitario, 2) }}
                        </td>

                        <td class="right">
                            {{ number_format($detalle->subtotal, 2) }}
                        </td>

                    </tr>

                </table>

                <div class="linea">
                    --------------------------------------------
                </div>

            </div>

        @endforeach

        <table>

            <tr>

                <td class="right total">
                    TOTAL A PAGAR:
                </td>

                <td width="35%" class="right total">
                    Bs. {{ number_format($venta->total, 2) }}
                </td>

            </tr>

        </table>

        <br>

        @php
            $metodos = explode(',', $venta->metodo_pago);

            $efectivo = in_array('EFECTIVO', $metodos);
            $transferencia = in_array('TRANSFERENCIA', $metodos);
            $qr = in_array('QR', $metodos);
        @endphp

        <br>

        <strong>FORMA DE PAGO</strong>

        <table style="width:100%; margin-top:5px;">
            <tr>
                <td width="80%">EFECTIVO</td>
                <td width="20%" align="center">
                    {!! $efectivo ? '[X]' : '[ ]' !!}
                </td>
            </tr>

            <tr>
                <td>TRANSFERENCIA</td>
                <td align="center">
                    {!! $transferencia ? '[X]' : '[ ]' !!}
                </td>
            </tr>

            <tr>
                <td>QR</td>
                <td align="center">
                    {!! $qr ? '[X]' : '[ ]' !!}
                </td>
            </tr>

            <tr>
                <td>OTRO</td>
                <td>________</td>
            </tr>
        </table>

        <div class="linea">
            --------------------------------------------
        </div>

        <div class="gracias">
            ¡GRACIAS POR SU COMPRA!<br>
            CONSERVE SU NOTA DE VENTA
        </div>


</body>

</html>