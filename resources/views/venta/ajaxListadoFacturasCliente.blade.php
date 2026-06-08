<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-8 gy-2" id="kt_table_facturas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Numero</th>
                <th>Prioridad</th>
                <th>Estado proceso</th>
                <th>Ultimo Proceso</th>
                <th>Nro OT</th>
                <th>Detalle</th>
                <th>Estado Factura</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($facturas as $fac)
                <tr>
                    <td>{{ $fac->nombres . ' ' . $fac->ap_paterno . ' ' . $fac->ap_materno }}</td>
                    <td>{{ $fac->fecha }}</td>
                    <td>
                        <span class="text-success">FAC: </span>{{ $fac->numero_factura }}
                    </td>
                    <td>
                        <span class="badge badge-success">{{ $fac->prioridad }}</span>
                    </td>
                    <td>
                        <span class="badge badge-warning">
                            {{ $fac->estado_ultimo_proceso }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-info">
                            {{ $fac->ultimo_proceso }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-dark">
                            {{ $fac->numero_ots }}
                        </span>
                    </td>

                    <td>
                        @forelse($fac->detalle_ot ?? [] as $d)
                            <div class="badge badge-light mb-1">{{ $d }}</div>
                        @empty
                            <span class="text-muted">SIN DATOS</span>
                        @endforelse

                    </td>
                    <td>
                        @if ($fac->estado == 'Anulado')
                            <span class="badge badge-danger">ANULADO</span>
                        @else
                            <span class="badge badge-success">VIGENTE</span>
                        @endif
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#kt_table_facturas').DataTable({
            lengthMenu: [10, 25, 50, 100],
            dom: 't<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
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