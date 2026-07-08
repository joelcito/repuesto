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
                <!-- Columna 1 -->
                <td width="40%" valign="top">
                    <table width="100%">
                        <tr>
                            <td><strong>Cliente:</strong></td>
                            <td>{{ $venta->cliente?->nombres }}
                                {{ $venta->cliente?->ap_paterno }}
                                {{ $venta->cliente?->ap_materno }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Celular:</strong></td>
                            <td>{{ $venta->cliente?->celular }}</td>
                        </tr>
                        <tr>
                            <td><strong>C.I.:</strong></td>
                            <td>{{ $venta->cliente?->cedula }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Columna 2 -->
                <td width="30%" valign="top">
                    <table width="100%">
                        <tr>
                            <td><strong>NIT:</strong></td>
                            <td>{{ $venta->cliente?->nit }}</td>
                        </tr>
                        <tr>
                            <td><strong>Usuario:</strong></td>
                            <td>
                                {{ $venta->usuario?->nombres }}
                                {{ $venta->usuario?->ap_paterno }}
                                {{ $venta->usuario?->ap_materno }}
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Columna 3 -->
                <td width="30%" valign="top">
                    <table width="100%">
                        <tr>
                            <td><strong>Fecha:</strong></td>
                            <td>{{ date('d/m/Y', strtotime($venta->fecha)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Hora:</strong></td>
                            <td>{{ date('H:i', strtotime($venta->created_at)) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th width="4%">N°</th>
                    <th width="10%">CÓDIGO</th>
                    <th width="25%">DESCRIPCIÓN DEL PRODUCTO</th>
                    <th width="12%">MARCA</th>
                    <th width="8%">CANT.</th>
                    <th width="8%">UNIDAD</th>
                    <th width="10%">P. UNIT.</th>
                    <th width="8%">DESC.</th>
                    <th width="10%">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp

                @foreach ($venta->detalles as $detalle)

                    @php
                        $total += $detalle->subtotal;
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detalle->producto?->codigo_interno }}</td>
                        <td>{{ $detalle->producto?->nombre }}</td>
                        <td>{{ $detalle->producto?->marca?->nombre ?? '-' }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ optional($detalle->producto->unidad)->nombre ?? '-' }}</td>

                        <td>{{ number_format($detalle->precio_unitario, 2) }}</td>

                        <td>{{ number_format($detalle->descuento, 2) }}</td>

                        <td>{{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>

                @endforeach
            </tbody>
            <tfoot>
                <!-- <tr>
                    <td colspan="8"> <strong>SUBTOTAL</strong> </td>
                    <td> {{ number_format($venta->subtotal, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="8"> <strong>DESCUENTO</strong> </td>
                    <td> {{ number_format($venta->descuento, 2) }} </td>
                </tr> 

                <tr>
                    <td colspan="8"><strong>CAMBIO</strong></td>
                    <td>{{ number_format($cambio, 2) }}</td>
                </tr> -->
                <tr>
                    <td colspan="8" style="text-align:right"> <strong>TOTAL A PAGAR</strong> </td>
                    <td> {{ number_format($venta->total, 2) }} </td>
                </tr>
                <!-- <tr>
                    <td colspan="8"> <strong>PAGADO</strong> </td>
                    <td> {{ number_format($totalPagado, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="8"> <strong>SALDO</strong> </td>
                    <td> {{ number_format($saldo, 2) }} </td>
                </tr> -->
                @php
                    $metodos = explode(',', $venta->metodo_pago);

                    $efectivo = in_array('EFECTIVO', $metodos);
                    $transferencia = in_array('TRANSFERENCIA', $metodos);
                    $qr = in_array('QR', $metodos);
                @endphp
                <tr>
                    <td width="20%"><strong>FORMA DE PAGO:</strong></td>

                    <td width="12%" align="right">EFECTIVO</td>
                    <td width="5%" align="center">
                        {!! $efectivo ? '☑' : '☐' !!}
                    </td>

                    <td width="18%" align="right">TRANSFERENCIA</td>
                    <td width="5%" align="center">
                        {!! $transferencia ? '☑' : '☐' !!}
                    </td>

                    <td width="8%" align="right">QR</td>
                    <td width="5%" align="center">
                        {!! $qr ? '☑' : '☐' !!}
                    </td>

                    <td width="8%" align="right">OTRO:</td>
                    <td style="border-bottom:1px solid #000;"></td>
                </tr>

            </tfoot>
        </table>
    </div>
</body>

</html>