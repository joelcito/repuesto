<div style="overflow-x: auto;">

    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_devolucion">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>ID</th>
                <th>Venta</th>
                <th>Tipo</th>
                <th>Total</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Actions</th>

        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($devoluciones as $devolucion)
                <tr>
                    <td>{{ $devolucion->id }}</td>
                    <td> VENTA #{{ $devolucion->venta_id }} </td>
                    <td>
                        <span class="badge badge-danger"> {{ $devolucion->tipo }} </span>
                    </td>
                    <td> Bs. {{ number_format($devolucion->total, 2) }} </td>
                    <td> {{ $devolucion->motivo }} </td>
                    <td> {{ $devolucion->created_at }} </td>
                    <td> {{ $devolucion->estado }} </td>
                    <td> <a href="{{ route('devolucion.detalledevolucion', $devolucion->id) }}" target="_blank"
                            class="btn btn-info btn-sm btn-icon" title="Ver detalle">
                            <i class="fa fa-eye"></i>
                        </a>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle"
                            onclick="eliminarDevolucion({{ $devolucion->id }})">
                            <i class="fa fa-trash"></i>
                        </button>
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
        $('#kt_table_devolucion').DataTable({
            lengthMenu: [10, 25, 50, 100],
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
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