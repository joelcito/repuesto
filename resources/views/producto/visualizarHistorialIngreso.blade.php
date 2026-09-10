<div class="row">
    <div class="col-md-12">
        {{ $producto->nombre }}
    </div>
</div>
<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_historicos_ingresos">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Cantidad</th>
                <th>Precio de Compra</th>
                <th>Precio de Venta</th>
                <th>Precio por Mayor</th>
                <th>Fecha</th>
                <th>Descripcion</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($movimientos as $movimiento)
            <tr>
                <td>{{ $movimiento->cantidad }}</td>
                <td>{{ $movimiento->precio_compra }}</td>
                <td>{{ $movimiento->precio_venta }}</td>
                <td>{{ $movimiento->precio_mayor }}</td>
                <td>{{ $movimiento->fecha }}</td>
                <td>{{ $movimiento->descripcion }}</td>
            </tr>
            @empty
                No hay productos registrados
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#kt_table_historicos_ingresos').DataTable({
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
