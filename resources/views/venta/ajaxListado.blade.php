<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_venta">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nro Venta</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Subtotal</th>
                <th>Descuento</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Estado Pago</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @php
                $totalGeneral = 0;
                $totalPagado = 0;
            @endphp
            @forelse($ventas as $venta)
                @php
                    $pagado = $venta->pagos->where('estado', 'ACTIVO')->sum('monto');
                    $totalGeneral += $venta->total;
                    $totalPagado += $pagado;
                @endphp
                <tr>
                    <td>
                        <span class="badge badge-info">
                            {{ sprintf('%06d', $venta->numero_factura) }}
                        </span>
                    </td>
                    <td>
                        {{ $venta->cliente?->nombres }}
                        {{ $venta->cliente?->ap_paterno }}
                    </td>
                    <td>
                        {{ date('d/m/Y', strtotime($venta->fecha)) }}
                    </td>
                    <td>
                        {{ number_format($venta->subtotal, 2) }}
                    </td>
                    <td>
                        {{ number_format($venta->descuento, 2) }}
                    </td>
                    <td>
                        <span class="fw-bold text-success">
                            {{ number_format($venta->total, 2) }}
                        </span>
                    </td>
                    <td>
                        {{ number_format($pagado, 2) }}
                    </td>
                    <td>
                        @if ($venta->estado_pago == 'PAGADO')
                            <span class="badge badge-success">
                                PAGADO
                            </span>
                        @elseif($venta->estado_pago == 'PARCIAL')
                            <span class="badge badge-warning">
                                PARCIAL
                            </span>
                        @else
                            <span class="badge badge-danger">
                                DEUDA
                            </span>
                        @endif
                    </td>
                    <td>
                        @if ($venta->estado == 'ANULADO')
                            <span class="badge badge-danger">
                                ANULADO
                            </span>
                        @else
                            <span class="badge badge-success">
                                ACTIVO
                            </span>
                        @endif
                    </td>
                    <td>
                        {{ $venta->usuarioCreador?->nombres }}
                    </td>
                    <td>
                        <a href="{{ route('venta.detalle', $venta->id) }}" class="btn btn-info btn-sm btn-icon"
                            title="Ver detalle">
                            <i class="fa fa-eye"></i>
                        </a>
                        <button class="btn btn-primary btn-sm btn-icon" onclick="imprimirRecibo('{{ $venta->id }}')"
                            title="Imprimir">
                            <i class="fa fa-file-pdf"></i>
                        </button>
                        @if($venta->estado != 'ANULADO')
                            <button class="btn btn-danger btn-sm btn-icon" onclick="anularVenta('{{ $venta->id }}')"
                                title="Anular">
                                <i class="fa fa-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">
                    <b>TOTALES</b>
                </td>
                <td>
                    <b>{{ number_format($totalGeneral, 2) }}</b>
                </td>
                <td>
                    <b>{{ number_format($totalPagado, 2) }}</b>
                </td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>
    <!--end::Table-->
</div>
<script>

    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#kt_table_venta')) {
            $('#kt_table_venta').DataTable().destroy();
        }
        $('#kt_table_venta').DataTable({
            lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
            language: {
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros por página',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            order: [],
            responsive: true
        });
    });
</script>