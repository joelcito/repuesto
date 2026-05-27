<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_venta">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">

                <th>Fecha</th>
                <th>Cliente</th>
                <th>Atendido Por</th>
                <th>Total</th>
                <th>Método Pago</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse($ventas as $venta)
                <tr>

                    <td>{{ date('d/m/Y H:i', strtotime($venta->created_at)) }}</td>
                    <td>{{ $venta->cliente->nombres ?? 'SIN CLIENTE' }}</td>
                    <td>{{ $venta->usuarioCreador->name ?? '' }}</td>
                    <td>Bs. {{ number_format($venta->total, 2) }}</td>
                    <td>{{ $venta->metodo_pago }}</td>
                    <td>
                        <span class="badge bg-success">
                            {{ $venta->estado }}
                        </span>
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
    <!--end::Table-->
</div>

<script>
    $(document).ready(function () {
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
            //  searching: true,
            responsive: true
        });


    });
</script>