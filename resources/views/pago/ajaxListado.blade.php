<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-7 gy-4" id="kt_table_users">
        <thead>
            <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                <th>Sucursal</th>
                <th></th>
                <th>Fecha</th>
                <th>Descripcion</th>
                <th>Tipo Pago</th>
                <th width="10px">Monto Efectivo</th>
                <th width="10px">Monto Deposito</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @php

                $totalIngresoEfectivo = 0;
                $totalIngresoQR = 0;
                $totalIngresoTransferencia = 0;

                $totalSalidaEfectivo = 0;
                $totalSalidaQR = 0;
                $totalSalidaTransferencia = 0;

                $totalVentas = 0;
                $totalOtrosIngresos = 0;
            @endphp
            @forelse ($pagos as $pago)
                <tr class="{{ $pago->estado == 'INGRESO' ? 'bg-light-success' : 'bg-light-danger' }}">

                    <td>{{ $pago?->sucursal?->nombre }}</td>
                    <td></td>
                    <td>{{ $pago->fecha }}</td>
                    <td>{{ $pago->descripcion }}</td>
                    <td>{{ $pago->tipo_pago }}</td>

                    {{-- EFECTIVO --}}
                    <td>
                        @if($pago->tipo_pago == 'EFECTIVO')
                            {{ number_format($pago->monto, 2) }}
                        @else
                            0.00
                        @endif
                    </td>

                    {{-- QR / TRANSFERENCIA --}}
                    <td>
                        @if($pago->tipo_pago == 'QR' || $pago->tipo_pago == 'TRANSFERENCIA')
                            {{ number_format($pago->monto, 2) }}
                        @else
                            0.00
                        @endif
                    </td>

                    <td>
                        @if ($pago->estado === 'INGRESO')
                            <span class="badge badge-success">
                                {{ $pago->estado }}
                            </span>
                        @else
                            <span class="badge badge-danger">
                                {{ $pago->estado }}
                            </span>
                        @endif
                    </td>

                    <td>{{ $pago->usuario->name }}</td>

                    <td>
                        <a target="_blank" href="{{ url('pago/comprobantePago', [$pago->id]) }}"
                            class="btn btn-icon btn-info btn-sm" title="Imprimir Comprobante">
                            <i class="fa fa-file"></i>
                        </a>
                    </td>
                </tr>

                @php

                    if ($pago->estado === 'INGRESO') {

                        if ($pago->tipo_pago === 'EFECTIVO') {
                            $totalIngresoEfectivo += $pago->monto;
                        }

                        if ($pago->tipo_pago === 'QR') {
                            $totalIngresoQR += $pago->monto;
                        }
                        if ($pago->tipo_pago === 'TRANSFERENCIA') {
                            $totalIngresoTransferencia += $pago->monto;
                        }
                        if ($pago->venta_id != null) {
                            $totalVentas += $pago->monto;
                        }

                        if ($pago->venta_id == null) {
                            $totalOtrosIngresos += $pago->monto;
                        }
                    }

                    if ($pago->estado === 'SALIDA') {

                        if ($pago->tipo_pago === 'EFECTIVO') {
                            $totalSalidaEfectivo += $pago->monto;
                        }
                        if ($pago->tipo_pago === 'QR') {
                            $totalSalidaQR += $pago->monto;
                        }

                        if ($pago->tipo_pago === 'TRANSFERENCIA') {
                            $totalSalidaTransferencia += $pago->monto;
                        }
                    }

                @endphp
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
            @php
                $saldoEfectivo =
                    $totalIngresoEfectivo - $totalSalidaEfectivo;
                $saldoQR =
                    $totalIngresoQR - $totalSalidaQR;
                $saldoTransferencia =
                    $totalIngresoTransferencia - $totalSalidaTransferencia;
                $saldoTotal =
                    $saldoEfectivo +
                    $saldoQR +
                    $saldoTransferencia;
            @endphp
        </tbody>
        <tfoot>
            <tr class="bg-light-dark">
                <th class="text-center" colspan="3">
                    <b>SALDO TOTAL CAJA</b>
                </th>
                <th class="text-center" colspan="2">
                    {{ number_format($saldoTotal, 2) }}
                </th>
                <th class="text-center" colspan="3">
                    <b>TOTAL VENTAS</b>
                </th>
                <th class="text-center" colspan="2">
                    {{ number_format($totalVentas, 2) }}
                </th>
            </tr>
            <tr>
                <th class="bg-light-success">
                    <b>EFECTIVO</b>
                </th>
                <th class="bg-light-success">
                    {{ number_format($saldoEfectivo, 2) }}
                </th>
                <th class="bg-light-info">
                    <b>QR</b>
                </th>
                <th class="bg-light-info">
                    {{ number_format($saldoQR, 2) }}
                </th>
                <th class="bg-light-primary">
                    <b>TRANSFERENCIA</b>
                </th>
                <th class="bg-light-primary">
                    {{ number_format($saldoTransferencia, 2) }}
                </th>
                <th class="bg-light-warning">
                    <b>OTROS INGRESOS</b>
                </th>
                <th class="bg-light-warning">
                    {{ number_format($totalOtrosIngresos, 2) }}
                </th>
                <th class="bg-light-danger">
                    <b>SALIDAS EFECTIVO</b>
                </th>
                <th class="bg-light-danger">
                    {{ number_format($totalSalidaEfectivo, 2) }}
                </th>

            </tr>
        </tfoot>
    </table>
    <!--end::Table-->
</div>

<script>
    $(document).ready(function () {
        $('#kt_table_users').DataTable({
            lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            // dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
            dom: '<"dt-head row"><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
            lengthChange: false,
            searching: false,
            language: {
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            order: [],
            responsive: true,
        });
    });
</script>