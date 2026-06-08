@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/jquery.orgchart.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
    <style>
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

<div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 90%">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">FORMULARIO DE DETALLE VENTA</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formularioOrdenTrabajo">
                    <div id="formularioAjaxOrdenTrabajo"></div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleVentaImpresion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">FORMULARIO DE IMPRESION POR DETALLE DE VENTA</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formularioOrdenTrabajoSelect">
                    <select class="form-select form-select-sm" name="numero_ot_select" id="numero_ot_select">
                    </select>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="imprimirOrdenTrabajo()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdicionEstadoOrdenTrabajo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h5 class="fw-bold">FORMULARIO CAMBIO DE ESTADO DE DETALLE DE VENTA <span class="text-info"
                        id="numero_orden_trabajo_text"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formularioCambioEstadoOrdenTrabajo">
                    <select class="form-select form-select-sm" name="estado_orden_trabajo" id="estado_orden_trabajo">
                        <option value="RECEPCIONADO">RECEPCIONADO</option>
                        <option value="ENTREGADO">ENTREGADO</option>
                    </select>
                    <input type="hidden" id="venta_id_estado" name="venta_id_estado" value="{{ $venta->id }}">
                    <input type="hidden" id="nro_ot_estado" name="nro_ot_estado" value="{{ $venta->id }}">
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success"
                            onclick="guardarEstadoOrdenTrabajo()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card">
                <div class="card-body py-4">
                    <div class="d-flex mb-9">
                        <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                            @if ($venta->cliente && $venta->cliente->imagen)
                                <div style="width: 200px; height: 200px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <img width="100%" height="100%"
                                                src="{{ asset('storage/imagenesClientes') }}/{{ $venta->cliente->imagen }}"
                                                height="110" alt="image">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <img src="{{ asset('assets/img/default.jpg') }}" height="110" alt="image">
                            @endif
                        </div>
                        <div class="flex-grow-1" style="margin-left: 10px;">
                            <div class="d-flex justify-content-between flex-wrap mt-1">
                                <div class="d-flex mr-3">
                                    <h2><span class="text-primary">CLIENTE: </span>
                                        {{ $venta->cliente?->nombres }}
                                        {{ $venta->cliente?->ap_paterno }}
                                        {{ $venta->cliente?->ap_materno }}
                                    </h2>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-3">
                                    <h6><span class="text-primary">CELULAR: </span>
                                        {{ $venta->cliente?->celular }}
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <h6><span class="text-primary">CEDULA: </span>
                                        {{ $venta->cliente?->cedula }}
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <h6><span class="text-primary">NIT: </span>
                                        {{ $venta->cliente?->nit }}
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <h6><span class="text-primary">RAZON SOCIAL: </span>
                                        {{ $venta->cliente?->razon_social }}
                                    </h6>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <h6><span class="text-primary">DIRECCION: </span>
                                        {{ $venta->cliente?->direccion }}
                                    </h6>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>

                    <div class="separator separator-solid"></div>
                    <div class="d-flex align-items-center flex-wrap mt-8">
                        <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                            <span class="mr-4">
                                <i class="fa-solid fa-money-bill-1-wave"
                                    style="font-size: 30px; margin-right: 5px;"></i>
                            </span>
                            <div class="d-flex flex-column text-dark-75">
                                <span class="font-weight-bolder font-size-sm text-primary">PRECIO</span>
                                <h5>{{ number_format($venta->total, 2) }}</h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                            <span>
                                <i class="fas fa-barcode" style="font-size: 30px; margin-right: 5px;"></i>
                            </span>
                            <div class="d-flex flex-column text-dark-75">
                                <span class="font-weight-bolder font-size-sm text-primary">DESCUENTO</span>
                                <h5>{{ number_format($venta->descuento, 2) }}</h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                            <span>
                                <i class="fas fa-barcode" style="font-size: 30px; margin-right: 5px;"></i>
                            </span>
                            <div class="d-flex flex-column text-dark-75">
                                <span class="font-weight-bolder font-size-sm text-primary">A CUENTA</span>
                                <h5>{{ number_format($venta->pagos->sum('monto'), 2) }}</h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                            <span class="mr-4">
                                <i class="fas fa-democrat" style="font-size: 30px; margin-right: 5px;"></i>
                            </span>
                            <div class="d-flex flex-column text-dark-75">
                                <span class="font-weight-bolder font-size-sm text-primary">SALDO</span>
                                <h5>{{ number_format((($venta->total - $venta->descuento) - $venta->pagos->sum('monto')), 2) }}
                                </h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                            <span class="mr-4">
                                <i class="fas fa-file-pdf" style="font-size: 30px; margin-right: 5px;"></i>
                            </span>
                            <div class="d-flex flex-column text-dark-75">
                                <span class="font-weight-bolder font-size-sm text-primary">NOTA VENTA</span>
                                <a target="_blank" href="{{ url('venta/recibo') }}/{{ $venta->id }}"
                                    class="btn btn-danger btn-sm btn-icon w-100"><i class="fa fa-file-pdf"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxlg">
                    <div class="card shadow-sm">
                        <div class="card-body py-4">
                            <div class="accordion accordion-icon-collapse" id="kt_accordion_3">
                                <div class="mb-5">
                                    <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse"
                                        data-bs-target="#kt_accordion_3_item_1">
                                        <span class="accordion-icon">
                                            <i class="ki-duotone ki-plus-square fs-3 accordion-icon-off"><span
                                                    class="path1"></span><span class="path2"></span><span
                                                    class="path3"></span></i>
                                            <i class="ki-duotone ki-minus-square fs-3 accordion-icon-on"><span
                                                    class="path1"></span><span class="path2"></span></i>
                                        </span>
                                        <h3 class="fs-4 fw-semibold mb-0 ms-4">REGISTRO DE DETALLE DE VENTA</h3>
                                    </div>
                                    <div id="kt_accordion_3_item_1" class="fs-6 collapse show ps-10"
                                        data-bs-parent="#kt_accordion_3">
                                        <div id="tabla-detalle-venta"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.orgchart.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        let filaTableLaser = 1;
        $(document).ready(function () {
            ajaxListadoDetalleVenta();
        });

        function ajaxListadoDetalleVenta() {
            let datos = { venta:{{ $venta->id }}};
            $.ajax({
                url: "{{ route('venta.ajaxListadoDetalleVenta') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado)
                        $('#tabla-detalle-venta').html(resultado.data.listado)
                }
            })
        }


        function agregarProducto() {

            if ($("#formularioNewOt")[0].checkValidity()) {
                Swal.fire({
                    title: "Esta seguro de agregar un nuevo OT?",
                    text: "Ya no podras revertir eso!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, agregar!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let datos = $('#formularioNewOt').serializeArray();
                        $.ajax({
                            url: "{{ url('venta/agregarNuevoOrdenTrabajo') }}",
                            method: "POST",
                            data: datos,
                            success: function (resultado) {
                                if (resultado.estado) {
                                    ajaxListadoDetalleVenta();
                                    Swal.fire(
                                        'Exito',
                                        'Se agrego con exito',
                                        'success'
                                    );

                                    $('#formularioAjaxOrdenTrabajo').html("")

                                }
                            }
                        })
                    }
                });
            } else {
                $("#formularioNewOt")[0].reportValidity();
            }
        }

        function calcularsubTotal() {
            let cantidad = parseFloat($('#cantidad_venta').val())
            let precio = parseFloat($('#precio_venta').val())

            $('#sub_total').val(cantidad * precio);
        }


        function ajaxListadoDetalleVenta() {

            $.ajax({

                url: "{{ route('venta.ajaxListadoDetalleVenta') }}",

                method: "POST",

                data: {
                    venta_id: {{ $venta->id }}
                            },

                success: function (resultado) {

                    if (resultado.estado) {

                        $('#tabla-detalle-venta').html(
                            resultado.data.listado
                        );

                    }

                }

            });

        }

    </script>
@endsection