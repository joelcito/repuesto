@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .tamanio_boton {
            font-size: 6px;
        }
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

<div class="modal fade" id="modalDevolucion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-danger">
                <h3 class="fw-bold"> <i class="fa fa-rotate-left me-2"></i> REGISTRO DE DEVOLUCIÓN </h3> <button
                    type="button" class="btn-close" data-bs-dismiss="modal"> </button>
            </div>
            <div class="modal-body">
                <form id="formularioDevolucion"> <input type="hidden" name="id" id="id">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title"> Datos Generales </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4"> <label class="fw-semibold fs-6 mb-2"> Venta </label> <select
                                        class="form-select form-select-sm" name="venta_id" id="venta_id">
                                        <option value=""> Seleccione </option> @foreach($ventas as $venta) <option
                                            value="{{ $venta->id }}"> VENTA #{{ $venta->id }} |
                                            {{ $venta->cliente->nombres ?? 'S/N' }} | Bs.
                                            {{ number_format($venta->total, 2) }}
                                        </option> @endforeach
                                    </select> </div>
                                <div class="col-md-4"> <label class="fw-semibold fs-6 mb-2"> Tipo </label> <select
                                        class="form-select form-select-sm" name="tipo" id="tipo">
                                        <option value="DINERO"> DEVOLUCIÓN DINERO </option>
                                        <option value="PRODUCTO"> DEVOLUCIÓN PRODUCTO </option>
                                    </select> </div>
                                <div class="col-md-4"> <label class="fw-semibold fs-6 mb-2"> Monto </label> <input
                                        type="number" step="0.01" class="form-control form-control-sm" name="monto"
                                        id="monto" readonly value="0"> </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12"> <label class="fw-semibold fs-6 mb-2"> Motivo </label> <textarea
                                        class="form-control form-control-sm" rows="3" name="motivo"
                                        id="motivo"></textarea> </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"> Productos de la Venta </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr class="fw-bold text-center">
                                            <th> Producto </th>
                                            <th width="120"> Cantidad Vendida </th>
                                            <th width="120"> Devuelto </th>
                                            <th width="140"> Precio </th>
                                            <th width="140"> Desc.</th>
                                            <th width="120">Precio Final</th>
                                            <th width="170"> Devolver </th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle_devolucion">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5"> Seleccione una venta
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"> <button type="button" class="btn btn-light" data-bs-dismiss="modal"> Cancelar
                </button> <button class="btn btn-danger" onclick="guardarDevolucion()"> <i class="fa fa-save me-1"></i>
                    Guardar Devolución </button> </div>
        </div>
    </div>
</div>
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-danger py-4 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-rotate-left me-2"></i>
                        Devoluciones
                    </h3>

                    <button class="btn btn-danger btn-sm" onclick="modalNuevaDevolucion()">
                        <i class="fa fa-plus"></i>
                        Nueva Devolución
                    </button>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">

                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabVentas">
                                <i class="fa fa-search me-2"></i>
                                Buscar Ventas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabHistorial">
                                <i class="fa fa-clock-rotate-left me-2"></i>
                                Historial de Devoluciones
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content">

                        <!-- TAB BUSCAR VENTAS -->
                        <div class="tab-pane fade show active" id="tabVentas">

                            <!-- AQUÍ VAN TUS FILTROS -->

                            <div class="row g-3 mb-4">


                                <div class="card shadow-sm mb-5">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fa fa-search me-2"></i>
                                            Buscar Venta
                                        </h3>
                                    </div>

                                    <div class="card-body">

                                        <div class="row g-3 mb-4">

                                            <div class="col-md-2">
                                                <label class="form-label">N° Venta</label>
                                                <input type="text" class="form-control" id="buscar_venta">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Cliente</label>
                                                <select id="cliente" class="form-select">
                                                    <option value="">Todos</option>

                                                    @foreach($clientes as $cliente)
                                                        <option value="{{ $cliente->id }}">
                                                            {{ $cliente->ap_paterno }}
                                                            {{ $cliente->ap_materno }}
                                                            {{ $cliente->nombres }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Vendedor</label>
                                                <select id="vendedor" class="form-select">

                                                    <option value="">Todos</option>

                                                    @foreach($vendedores as $vendedor)
                                                        <option value="{{ $vendedor->id }}">
                                                            {{ $vendedor->nombres }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Desde</label>
                                                <input type="date" class="form-control" id="fecha_desde">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Hasta</label>
                                                <input type="date" class="form-control" id="fecha_hasta">
                                            </div>

                                        </div>

                                        <div id="tablaVentas"></div>

                                    </div>
                                </div>
                            </div>

                            <div id="tablaVentas"></div>

                        </div>

                        <!-- TAB HISTORIAL -->
                        <div class="tab-pane fade" id="tabHistorial">
                            <div id="table_listado"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop()
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $(document).ready(function () {
            ajaxListado();
            buscarVentas();
        });

        $('#buscar_venta,#cliente,#vendedor,#fecha_desde,#fecha_hasta')
            .on('keyup change', function () {
                buscarVentas();
            });


        function ajaxListado() {
            $.ajax({
                url: "{{ route('devolucion.ajaxListado') }}",
                method: "POST",
                success: function (resultado) {

                    $('#table_listado').html(resultado.data.listado);
                    iniciarTabla('#kt_table_devolucion');
                    // if ($.fn.DataTable.isDataTable('#kt_table_devolucion')) {
                    //     $('#kt_table_devolucion').DataTable().destroy();
                    // }

                    // $('#kt_table_devolucion').DataTable({
                    //     responsive: true,
                    //     destroy: true
                    // });

                }
            });
        }

        function modalNuevaDevolucion() {
            $('#formularioDevolucion')[0].reset();
            $('#monto').val(0);
            $('#detalle_devolucion').html(` <tr> <td colspan="5" class="text-center text-muted py-5"> Seleccione una venta </td> </tr> `);
            $('#modalDevolucion').modal('show');
        }

        $('#venta_id').change(function () {

            let venta_id = $(this).val();

            if (venta_id == '') {
                return;
            }

            $.ajax({
                url: "{{ route('devolucion.obtenerDetalleVenta') }}",
                method: "POST",
                data: {
                    venta_id: venta_id
                },
                success: function (resultado) {

                    let html = '';

                    resultado.data.forEach(item => {

                        let devuelto = item.cantidad_devuelta ?? 0;
                        let disponible = item.cantidad - devuelto;

                        let descuentoUnitario =
                            (item.descuento ?? 0) / item.cantidad;

                        let precioFinal =
                            item.precio_unitario - descuentoUnitario;

                        html += `
                    <tr>

                        <td>${item.producto.nombre}</td>

                        <td class="text-center">
                            ${item.cantidad}
                        </td>

                        <td class="text-center text-danger fw-bold">
                            ${devuelto}
                        </td>

                        <td class="text-center">
                            Bs. ${parseFloat(item.precio_unitario).toFixed(2)}
                        </td>

                        <td class="text-center text-warning">
                            Bs. ${parseFloat(descuentoUnitario).toFixed(2)}
                        </td>

                        <td class="text-center text-success fw-bold">
                            Bs. ${parseFloat(precioFinal).toFixed(2)}
                        </td>

                        <td>

                            <input
                                type="number"
                                min="0"
                                max="${disponible}"
                                value="0"
                                class="form-control form-control-sm cantidad_devolucion"
                                data-precio="${item.precio_unitario}"
                                data-descuento="${item.descuento}"
                                data-cantidad="${item.cantidad}"
                                data-producto="${item.producto_id}">

                            <small class="text-danger">
                                Disponible: ${disponible}
                            </small>

                        </td>

                    </tr>`;
                    });

                    $('#detalle_devolucion').html(html);

                    calcularMonto();
                }
            });

        });

        function calcularMonto() {
            let total = 0;
            $('.cantidad_devolucion').each(function () {
                let cantidad = parseFloat($(this).val()) || 0;
                let precio = parseFloat($(this).data('precio')) || 0;
                //total += cantidad * precio;
                let descuento =
                    parseFloat($(this).data('descuento')) || 0;

                let cantidadVendida =
                    parseFloat($(this).data('cantidad')) || 1;

                let descuentoUnitario = descuento / cantidadVendida;

                let subtotal =
                    cantidad * (precio - descuentoUnitario);

                total += subtotal;
            });
            $('#monto').val(total.toFixed(2));
        }

        $(document).on('keyup change', '.cantidad_devolucion', function () { calcularMonto(); });

        function guardarDevolucion() {
            let productos = []; $('.cantidad_devolucion').each(function () {
                let cantidad = parseFloat($(this).val()) || 0; if (cantidad > 0) {
                    productos.push({ producto_id: $(this).data('producto'), cantidad: cantidad });
                }
            }); if (productos.length == 0) {
                Swal.fire({ icon: 'warning', title: 'Debe seleccionar al menos un producto' });
                return;
            } let datos = {
                venta_id: $('#venta_id').val(), tipo: $('#tipo').val(),
                motivo: $('#motivo').val(), productos: productos
            }; $.ajax({
                url: "{{ route('devolucion.guardarDevolucion') }}",
                method: "POST", data: datos, success: function (resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            icon: 'success', title: resultado.mensaje, timer: 2000, showConfirmButton: false
                        }); $('#modalDevolucion').modal('hide');
                        ajaxListado();
                    } else { Swal.fire({ icon: 'error', title: resultado.mensaje }); }
                }, error: function () {
                    Swal.fire({ icon: 'error', title: 'Ocurrió un error inesperado' });
                }
            });
        }


        function eliminarDevolucion(id) {
            Swal.fire({
                title: '¿Eliminar devolución?',
                text: 'Esta acción no se puede revertir',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('devolucion.eliminarDevolucion') }}",
                        method: "POST",
                        data: {
                            id: id
                        },
                        success: function (resultado) {
                            if (resultado.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: resultado.mensaje,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                ajaxListado();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: resultado.mensaje
                                });
                            }
                        },

                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al eliminar'
                            });
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        }

        function buscarVentas() {
            $.ajax({
                url: "{{ route('devolucion.buscarVentas') }}",
                method: "POST",
                data: {
                    numero: $('#buscar_venta').val(),
                    cliente: $('#cliente').val(),
                    vendedor: $('#vendedor').val(),
                    desde: $('#fecha_desde').val(),
                    hasta: $('#fecha_hasta').val()
                },
                success: function (resultado) {

                    $('#tablaVentas').html(resultado.html);
                    iniciarTabla('#kt_table_ventas');

                    if ($.fn.DataTable.isDataTable('#kt_table_ventas')) {
                        $('#kt_table_ventas').DataTable().destroy();
                    }

                    $('#kt_table_ventas').DataTable({
                        responsive: true,
                        destroy: true,
                        order: [[0, 'desc']]
                    });

                }
            });
        }

        function seleccionarVenta(id) {

            $('#venta_id').val(id);
            $('#modalDevolucion').modal('show');

            $.ajax({
                url: "{{ route('devolucion.obtenerDetalleVenta') }}",
                method: "POST",
                data: {
                    venta_id: id
                },
                success: function (resultado) {

                    let html = '';

                    resultado.data.forEach(item => {

                        let devuelto = item.cantidad_devuelta ?? 0;
                        let disponible = item.cantidad - devuelto;

                        let descuentoUnitario =
                            (item.descuento ?? 0) / item.cantidad;

                        let precioFinal =
                            item.precio_unitario - descuentoUnitario;

                        html += `
                            <tr>

                                <td>${item.producto.nombre}</td>

                                <td class="text-center">
                                    ${item.cantidad}
                                </td>

                                <td class="text-center text-danger">
                                    ${devuelto}
                                </td>

                                <td class="text-center">
                                    Bs. ${parseFloat(item.precio_unitario).toFixed(2)}
                                </td>

                                <td class="text-center text-warning">
                                    Bs. ${parseFloat(descuentoUnitario).toFixed(2)}
                                </td>

                                <td class="text-center text-success fw-bold">
                                    Bs. ${parseFloat(precioFinal).toFixed(2)}
                                </td>

                                <td>

                                    <input
                                        type="number"
                                        min="0"
                                        max="${disponible}"
                                        value="0"
                                        class="form-control cantidad_devolucion"
                                        data-precio="${item.precio_unitario}"
                                        data-descuento="${item.descuento}"
                                        data-cantidad="${item.cantidad}"
                                        data-producto="${item.producto_id}">

                                    <small class="text-danger">
                                        Disponible: ${disponible}
                                    </small>

                                </td>

                            </tr>`;
                    });

                    $('#detalle_devolucion').html(html);

                    calcularMonto();
                }
            });
        }


        function iniciarTabla(id) {

            if ($.fn.DataTable.isDataTable(id)) {
                $(id).DataTable().destroy();
            }

            $(id).DataTable({
                destroy: true,
                responsive: true,
                lengthMenu: [10, 25, 50, 100],
                order: [[0, 'desc']],
                dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>>t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
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
                }
            });

        }
    </script>
@endsection