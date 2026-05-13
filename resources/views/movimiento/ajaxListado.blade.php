<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_stock">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nombre</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($sucursales as $sucursal)
                <tr>
                    <td>{{ $sucursal->nombre }}</td>
                    @php

                        $ingresos = $sucursal->movimientos
                            ->where('producto_id', $productoId)
                            ->where('tipo', 'ingreso')
                            ->sum('cantidad');

                        $salidas = $sucursal->movimientos
                            ->where('producto_id', $productoId)
                            ->where('tipo', 'salida')
                            ->sum('cantidad');

                        $stock = $ingresos - $salidas;

                    @endphp

                    <td>{{ $stock }}</td>
                    <td>
                        {{-- <button class="btn btn-icon btn-sm btn-success btn-circle" title="Ingreso producto"
                            onclick="modalIngreso({{ $productoId }}, {{ $movimiento->sucursal_id }},{{ json_encode($movimiento->sucursal_nombre) }})">+</button>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Salida producto"
                            onclick="modalSalida({{ $productoId }}, {{ $movimiento->sucursal_id }},{{ json_encode($movimiento->sucursal_nombre) }})">-</button>
                        --}}
                        <button class="btn btn-icon btn-sm btn-success btn-circle" title="Ingreso producto"
                            onclick="modalIngreso({{ $productoId }}, {{ $sucursal->id }},{{ json_encode($sucursal->nombre) }})">+</button>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Salida producto"
                            onclick="modalSalida({{ $productoId }}, {{ $sucursal->id }},{{ json_encode($sucursal->nombre) }})">-</button>
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
        $('#kt_table_stock').DataTable({
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