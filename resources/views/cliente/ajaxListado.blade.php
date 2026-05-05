<div style="overflow-x: auto;">

    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_usuarios">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Celular</th>
                <th>C.I.</th>
                <th>NIT</th>
                <th>Razon Social</th>
                <th>Direccion</th>
                <th>Actions</th>
                <th>Referencias</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nombres }}</td>
                    <td>{{ $cliente->ap_paterno }}</td>
                    <td>{{ $cliente->ap_materno }}</td>
                    <td>{{ $cliente->celular }}</td>
                    <td>{{ $cliente->cedula }}</td>
                    <td>{{ $cliente->nit }}</td>
                    <td>{{ $cliente->razon_social }}</td>
                    <td>{{ $cliente->direccion}}</td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-warning btn-circle" title="Editar cliente"
                            onclick="editarCliente({{ json_encode($cliente) }})"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Eliminar cliente"
                            onclick="eliminarCliente('{{ $cliente->id }}',  '{{ $cliente->razon_social }}')"><i
                                class="fa fa-trash"></i></button>
                    </td>
                    <td>
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_usuarios">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th>Nombre Completo</th>
                                    <th>Celular</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                <tr>
                                    <td>{{ $cliente->nombre_referencia_1 }}</td>
                                    <td>{{ $cliente->celular_referencia_1 }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $cliente->nombre_referencia_2 }}</td>
                                    <td>{{ $cliente->celular_referencia_2 }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $cliente->nombre_referencia_3 }}</td>
                                    <td>{{ $cliente->celular_referencia_3 }}</td>
                                </tr>
                            </tbody>
                        </table>
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
        $('#kt_table_usuarios').DataTable({
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