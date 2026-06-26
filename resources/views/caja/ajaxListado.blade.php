<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_caja">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>ID</th>
                <th>Usuario</th>
                <th>Sucursal</th>
                <th>Apertura</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Cierre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse($cajas as $caja)
                <tr>
                    <td>{{ $caja->id }}</td>
                    <td>{{ $caja->usuario->name ?? '' }}</td>
                    <td>{{ $caja->sucursal->nombre ?? '' }}</td>
                    <td> Bs. {{ number_format($caja->monto_apertura, 2) }}</td>
                    <td> Bs. {{ number_format($caja->total_ingresos, 2) }}</td>
                    <td> Bs. {{ number_format($caja->total_egresos, 2) }}</td>
                    <td>Bs. {{ number_format($caja->monto_cierre, 2) }}</td>
                    <td>
                        @if($caja->estado == 'ABIERTA')
                            <span class="badge bg-success">
                                ABIERTA
                            </span>
                        @else
                            <span class="badge bg-danger">
                                CERRADA
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($caja->estado == 'ABIERTA')
                            <button type="button" class="btn btn-sm btn-light-danger fw-bold"
                                onclick="cerrarCaja({{ $caja->id }})">
                                <i class="fa fa-lock me-1"></i>
                                Cerrar Caja
                            </button>
                        @else
                            <span class="badge badge-light-success px-4 py-2">
                                <i class="fa fa-check-circle me-1"></i>
                                Cerrada
                            </span>
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
        $('#kt_table_caja').DataTable({
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