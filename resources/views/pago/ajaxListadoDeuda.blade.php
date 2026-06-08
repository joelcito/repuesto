<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_ventas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Sucursal</th>
                <th>Fecha Venta</th>
                <th>Asesor</th>
                <th>Cliente</th>
                <th>Fac / Or</th>
                <th>Total</th>
                <th>Descuento</th>
                <th>Sub Total</th>
                <th>A Cuenta</th>
                <th>Saldo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($ventas as $venta)
                <tr>
                    <td>{{ optional($venta->sucursal)->nombre }}</td>
                    <td>{{ date('d/m/Y H:i:s', strtotime($venta->fecha)) }}</td>
                    <td>{{ $venta->usuarioCreador->nombres . ' ' . $venta->usuarioCreador->ap_paterno }}</td>
                    <td>{{ optional($venta->cliente)->nombres . ' ' . optional($venta->cliente)->ap_paterno . ' ' . optional($venta->cliente)->ap_materno }}
                    </td>
                    <td>
                        <span class="text-info">
                            {{ sprintf('%06d', $venta->numero_venta) }}
                        </span>
                    </td>
                    <td>
                        {{ number_format($venta->total, 2) }}
                    </td>
                    <td>
                        {{ number_format($venta->descuento_adicional, 2) }}
                    </td>
                    <td>
                        <span class="text-warning">
                            {{ number_format($venta->total - $venta->descuento_adicional, 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="text-success">
                            {{ number_format($venta->pagos->sum('monto'), 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="text-danger">
                            {{ number_format(($venta->total - $venta->descuento_adicional) - $venta->pagos->sum('monto'), 2) }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-info btn-circle" title="Registrar Pago"
                            onclick="registrarPago({{ json_encode($venta) }})"><i class="fa fa-dollar"></i></button>
                        <button onclick="formularioDecuentoAdicional({{ $venta->id }})"
                            class="btn btn-icon btn-warning btn-circle btn-sm" title="Registrar Descuento">
                            <i class="fa fa-minus-square"></i>
                        </button>
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
        $('#kt_table_ventas').DataTable({
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