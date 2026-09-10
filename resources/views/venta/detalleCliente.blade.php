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
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card">
                <div class="card-body py-4">
                    <div class="d-flex mb-9">
                        <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                            @if ($cliente->imagen)
                                <div style="width: 200px; height: 200px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <img width="100%" height="100%"
                                                src="{{ asset('storage/imagenesClientes') }}/{{ $cliente->imagen }}"
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
                                        {{ $cliente->nombres . " " . $cliente->ap_paterno . " " . $cliente->ap_materno }}
                                    </h2>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-3">
                                    <h6><span class="text-primary">CELULAR: </span>
                                        {{ $cliente->celular }}
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <h6><span class="text-primary">CEDULA: </span>
                                        {{ $cliente->cedula }}
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <h6><span class="text-primary">NIT: </span>
                                        {{ $cliente->nit }}
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <h6><span class="text-primary">RAZON SOCIAL: </span>
                                        {{ $cliente->razon_social }}
                                    </h6>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <h6><span class="text-primary">DIRECCION: </span>
                                        {{ $cliente->direccion }}
                                    </h6>
                                </div>
                            </div>
                            <hr>

                        </div>
                    </div>
                    <div class="separator separator-solid"></div>
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
                                        <div id="tabla-orden-trabjo"></div>
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
            ajaxListadoOrdenTrabajos();
        });

        function ajaxListadoOrdenTrabajos() {
            let datos = { factura:{{ $factura->id }}};
            $.ajax({
                url: "{{ route('ordenTrabajo.ajaxListadoOrdenTrabajosCliente') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado)
                        $('#tabla-orden-trabjo').html(resultado.data.listado)
                }
            })
        }

        function ajaxFormularioEditarOrdenTrabajo(orden) {
            $.ajax({
                url: "{{ route('ordenTrabajo.ajaxFormularioEditarOrdenTrabajo') }}",
                method: "POST",
                data: { factura:{{ $factura->id }}},
                success: function (resultado) {
                    if (resultado.estado) {
                        $('#formularioAjaxOrdenTrabajo').html(resultado.data.listado)
                        $('#modalOrdenTrabajo').modal('show')
                    }
                }
            })
        }

        function ajaxNroOtFactura() {
            $.ajax({
                url: "{{ route('ordenTrabajo.ajaxNroOtFactura') }}",
                method: "POST",
                data: { factura:{{ $factura->id }}},
                success: function (resultado) {
                    if (resultado.estado) {
                        let listado = resultado.data.listaOt
                        $('#numero_ot_select').empty().append('<option value="">Seleccione una OT</option>');
                        $.each(listado, function (i, element) {
                            $('#numero_ot_select').append(
                                $('<option>', {
                                    value: element.nro_ot,
                                    text: 'OT ' + element.nro_ot
                                })
                            );
                        });
                        $('#modalOrdenTrabajoImpresion').modal('show')
                    }
                }
            })
        }

        function imprimirOrdenTrabajo() {
            let select = $('#numero_ot_select').val();

            if (!select) {
                Swal.fire(
                    'Error',
                    'Seleccione una OT',
                    'error'
                );
                return;
            }

            let url = "{{ route('ordenTrabajo.imprimirOrdenTrabajo', ['factura_id' => '__FACTURA__', 'nro_orden' => '__OT__']) }}"
                .replace('__FACTURA__', {{ $factura->id }})
                .replace('__OT__', select);
            window.open(url, '_blank');
        }

        function editarEstadoOrdenTrabajo(orden, estado) {
           

            $('#numero_orden_trabajo_text').text(orden)
            $('#estado_orden_trabajo').val(estado)
            $('#nro_ot_estado').val(orden)

            $('#modalEdicionEstadoOrdenTrabajo').modal('show')
        }

        function guardarEstadoOrdenTrabajo() {
            let datos = $('#formularioCambioEstadoOrdenTrabajo').serializeArray();
            $.ajax({
                url: "{{ route('ordenTrabajo.guardarEstadoOrdenTrabajo') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado) {
                        ajaxListadoOrdenTrabajos();
                        Swal.fire(
                            'Exito',
                            'Se Actualizo el estado con exito',
                            'success'
                        );
                        $('#modalEdicionEstadoOrdenTrabajo').modal('hide')
                    }
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
                            url: "{{ url('factura/agregarNuevoOrdenTrabajo') }}",
                            method: "POST",
                            data: datos,
                            success: function (resultado) {
                                if (resultado.estado) {
                                    ajaxListadoOrdenTrabajos();
                                    Swal.fire(
                                        'Exito',
                                        'Se agrego con exito',
                                        'success'
                                    );
                                    ajaxListadoOjales();
                                    $('#formularioAjaxOrdenTrabajo').html("")
                                    ajaxFormularioEditarOrdenTrabajo();
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

        function cambiarDato(tipo, ordenTrabajo, dato) {

            $.ajax({
                url: "{{ route('ordenTrabajo.cambiaDatoOrdenTrabajo') }}",
                method: "POST",
                data: {
                    tipo: tipo,
                    ordenTrabajo: ordenTrabajo,
                    dato: dato.value
                },
                success: function (resultado) {
                    if (resultado.estado) {
                        $('#' + tipo + "_" + ordenTrabajo).show('toggle')
                    } else {
                        Swal.fire(
                            'Error',
                            'Ocurrio un error',
                            'error'
                        );
                    }
                }
            })
        }
    </script>
@endsection