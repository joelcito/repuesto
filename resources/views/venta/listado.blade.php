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

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">


                <div class="card-header flex-wrap bg-light-info py-4">
                    <h3
                        class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        LISTADO DE VENTAS</h3>
                    <div class="card-toolbar">
                        <a class="btn btn-sm fw-bold btn-primary" href="{{ url('venta/formulario') }}"><i
                                class="fa fa-plus"></i>Nueva Venta Compra Venta</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form id="formulario-busqueda-factura">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="fw-semibold fs-6 mb-2">No. Factura</label>
                                <input type="number" class="form-control form-control-sm" name="buscar_nro_factura"
                                    id="buscar_nro_factura">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-semibold fs-6 mb-2">Nombre Cliente</label>
                                <input type="text" class="form-control form-control-sm" name="buscar_nombre_cliente"
                                    id="buscar_nombre_cliente">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-semibold fs-6 mb-2">C.I. Persona</label>
                                <input type="number" class="form-control form-control-sm" name="buscar_nro_cedula"
                                    id="buscar_nro_cedula">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-semibold fs-6 mb-2">NIT</label>
                                <input type="number" class="form-control form-control-sm" name="buscar_nit"
                                    id="buscar_nit">
                            </div>
                            <div class="col-md-1">
                                <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                <input type="date" class="form-control form-control-sm" name="buscar_fecha_inicio"
                                    id="buscar_fecha_inicio">
                            </div>
                            <div class="col-md-1">
                                <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                <input type="date" class="form-control form-control-sm" name="buscar_fecha_fin"
                                    id="buscar_fecha_fin">
                            </div>
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button type="button" id="botom_genera_buscar"
                                            class="btn btn-success btn-sm w-100 mt-8 btn-icon"
                                            onclick="ajaxListado()"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" id="botom_genera_pdf"
                                            class="btn btn-danger btn-sm w-100 btn-icon mt-8" title="Expotar en PDF"
                                            onclick="reportePDF()"><i class="fa fa-file-pdf"></i></button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" id="botom_genera_excel"
                                            class="btn btn-success btn-sm w-100 btn-icon mt-8" title="Expotar en Excel"
                                            onclick="exportarExcel()"><i class="fa fa-file-excel"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body py-4" id="table_listado">
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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $(document).ready(function () {
            console.log('dddd');
            ajaxListado();
        });

        function ajaxListado() {
            console.log('ENTRO AJAX');
            Swal.fire({
                title: 'Generando Listado...',
                text: 'Por favor espera mientras generamos el listado.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let datos = $('#formulario-busqueda-factura').serializeArray();

            $.ajax({
                url: "{{ url('venta/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (data) {

                    if (data.estado) {
                        $('#table_listado').html(data.data.listado)
                    }

                    Swal.close();
                },
                error: function (xhr) {
                    console.log(xhr.responseText);

                    Swal.close();

                    Swal.fire(
                        'Error',
                        xhr.responseText,
                        'error'
                    );
                }
            })
        }



        function reportePDF() {

            let datos = $('#formulario-busqueda-factura').serializeArray();
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Por favor espera mientras generamos el archivo.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('factura/reportePDF') }}",
                method: "POST",
                data: datos,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (data, status, xhr) {
                    Swal.close();
                    var blob = new Blob([data], {
                        type: 'application/pdf'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "reporte_facturas.pdf";
                    link.click();
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el PDF. Inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error("Error al generar el PDF: ", error);
                }
            });

        }

        function exportarExcel() {
            let datos = $('#formulario-busqueda-factura').serializeArray();

            Swal.fire({
                title: 'Generando Excel...',
                text: 'Por favor espera mientras generamos el archivo.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('factura/reporteExcel') }}",
                method: "POST",
                data: datos,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (data, status, xhr) {
                    Swal.close();
                    var blob = new Blob([data], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'reporte_facturas.xlsx';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function (xhr, status, error) {

                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el PDF. Inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error("Error al generar el PDF: ", error);
                }
            });
        }


        function limpiarErorres() {
            $('.error-message').html('');
            $('.is-invalid').removeClass('is-invalid');
        }

        function cambiaEstadoVenta(factura, estado) {
            let estados = [];
            if (estado == 'RECEPCIONADO') {
                estados = {
                    'TRABAJANDO': 'TRABAJANDO',
                    'TERMINADO': 'TERMINADO'
                }
            } else if (estado == 'TRABAJANDO') {
                estados = {
                    'TERMINADO': 'TERMINADO'
                }
            } else if (estado == 'TERMINADO') {
                estados = {
                    'ENTREGADO': 'ENTREGADO'
                }
            }

            Swal.fire({
                title: "SELECCIONE UN ESTADO",
                input: "select",
                inputOptions: estados,
                inputPlaceholder: 'Selecciona',
                showCancelButton: true,
                confirmButtonText: "Buscar",
                showLoaderOnConfirm: true,
                icon: 'question',
                preConfirm: async (login) => {

                    let datos = {
                        estado: login,
                        factura: factura
                    }

                    $.ajax({
                        url: "{{ url('factura/cambioEstadoVenta') }}",
                        method: "POST",
                        data: datos,
                        success: function (data) {

                            if (data.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "EXITO!",
                                    text: "SE CAMBIO DE ESTADO CON EXITO",
                                })
                                ajaxListado();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.mensaje.descripcion.codigoDescripcion,
                                    text: JSON.stringify(data.mensaje.descripcion
                                        .mensajesList),
                                })
                            }
                        }
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {

            });


        }

        // function imprimeREcibo(recibo) {
        //     href = "{{ url('factura/recibo') }}/" + recibo;
        //     window.open(href, '_blank');
        // }


        function anularRecibo(recibo, numero) {
            Swal.fire({
                title: "Esta seguro de Anular el numero de recibo " + numero + "?",
                text: "Esta accion no se podra revertir!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, estoy seguro!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('factura/anularRecibo') }}",
                        method: "POST",
                        data: { recibo: recibo },
                        dataType: 'json',
                        success: function (data) {
                            if (data.estado) {
                                ajaxListado();
                                Swal.fire({
                                    icon: 'success',
                                    title: "EXITO",
                                    text: JSON.stringify(data.msg),
                                    showConfirmButton: false,
                                    timerProgressBar: true
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: JSON.stringify(data.msg),
                                    title: "ERROR",
                                    showConfirmButton: false,
                                    timerProgressBar: true
                                });
                            }
                        }, error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.' + xhr,
                            });
                        }
                    })
                }
            });
        }


        function imprimirRecibo(venta_id) {
            let url = "{{ url('venta/recibo') }}/" + venta_id;
            window.open(url, '_blank');
        }

        function imprimirTiquet(venta_id) {
            let url = "{{ url('venta/tiquet') }}/" + venta_id;
            window.open(url, '_blank');
        }

        function anularVenta(venta_id) {
            Swal.fire({
                title: '¿Anular venta?',
                text: 'Esta acción devolverá el stock',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, anular'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ url('venta/anularVenta') }}",
                        method: "POST",
                        data: {
                            venta_id: venta_id
                        },
                        success: function (data) {
                            if (data.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: data.mensaje
                                });
                                ajaxListado();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.mensaje
                                });
                            }
                        },
                        error: function (xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        }
    </script>
@endsection