<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_incorporacion">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>ID</th>
                <th>Producto</th>
                <th>Descripcion</th>
                <th>Fecha</th>
                <th>Medidas</th>

                <th>Imagen</th>
                <th>Estado</th>
                <th>Actions</th>

            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse($incorporaciones as $incorporacion)
                <tr>
                    <td>{{ $incorporacion->id }}</td>
                    <td>
                        @if($incorporacion->producto)
                            {{ $incorporacion->producto->nombre }}
                        @else
                            {{ $incorporacion->nombre_producto }}
                        @endif
                    </td>
                    <td>
                        {{ $incorporacion->descripcion_producto }}
                    </td>
                    <td> {{ $incorporacion->created_at }} </td>
                    <td>{{ $incorporacion->medidas }}</td>
                    <td>
                        @if($incorporacion->imagenes->count())
                            <img src="{{ asset($incorporacion->imagenes->first()->ruta) }}" width="60" class="img-thumbnail">
                        @endif
                    </td>
                    <td>{{ $incorporacion->estado }}</td>
                    <td>
                        @if(!$incorporacion->producto_id)
                            <button class="btn btn-success" onclick="convertirAProducto({{ $incorporacion->id }})">
                                <i class="fa fa-box"></i>
                            </button>
                        @endif
                        @if($incorporacion->estado === 'ACTIVO')
                            <button class="btn btn-icon btn-sm btn-danger btn-circle"
                                onclick="eliminarIncorporacion({{ $incorporacion->id }})">
                                <i class="fa fa-trash"></i>
                            </button>
                        @endif
                    </td>
            </tr> @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>

</div>

<script>
    $(document).ready(function () {
        $('#kt_table_incorporacion').DataTable({
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