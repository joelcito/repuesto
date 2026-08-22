<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_producto">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Código Barras</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Precio Venta</th>
                <th>Imagen</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($productos as $producto)
                <tr>

                    <td>{{ $producto->codigo_barras }}</td>
                    <td>{{ $producto->codigo_interno }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->categoria->nombre ?? '' }}</td>
                    <td>{{ $producto->marca?->nombre }}</td>
                    <td>
                        {{ number_format($producto->precio_venta, 2) }}
                    </td>

                    @php
                        $imagen = $producto->imagenes->first()->imagen ?? null;
                    @endphp

                    <td>
                        <img src="{{ $imagen ? asset('imagenes/productos/' . $imagen) : asset('imagenes/productos/default.jpg') }}"
                            width="60" class="img-thumbnail">
                    </td>


                    <td>
                        <span class="badge {{ $producto->stock_actual > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $producto->stock_actual ?? 0 }}
                        </span>
                    </td>
                    <td>
                        @if($producto->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-dark btn-circle" title="Imprimir Código de Barras"
                            onclick="mostrarCodigoBarras({{ $producto->id }})"><i class="fa fa-barcode"></i></button>
                        <button class="btn btn-icon btn-sm btn-info btn-circle" title="Ver Stock"
                            onclick="abrirStock({{ $producto->id }}, '{{ $producto->nombre }}')"><i
                                class="fa fa-boxes"></i></button>
                        <button class="btn btn-icon btn-sm btn-warning btn-circle" title="Editar producto"
                            onclick="editarProducto({{ json_encode($producto) }})"><i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Eliminar producto"
                            onclick="eliminarProducto('{{ $producto->id }}', '{{ $producto->nombre }}')"> <i
                                class="fa fa-trash"></i>
                        </button>
                    </td>

                </tr>
            @empty
                No hay productos registrados
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#kt_table_producto').DataTable({
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