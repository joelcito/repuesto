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
            <div class="card">
                <div class="card-header flex-wrap bg-light-info py-4">
                    <h3
                        class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        LISTADO DE NOTAS DE RECEPCION</h3>
                </div>
                <div class="card-body py-4">
                    <form id="formulario-busqueda-factura">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="fw-semibold fs-6 mb-2">No. Factura</label>
                                <input type="number" class="form-control form-control-sm" name="buscar_nro_factura"
                                    id="buscar_nro_factura">
                            </div>
                            <div class="col-md-3">
                                <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                <input type="date" class="form-control form-control-sm" name="buscar_fecha_inicio"
                                    id="buscar_fecha_inicio">
                            </div>
                            <div class="col-md-3">
                                <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                <input type="date" class="form-control form-control-sm" name="buscar_fecha_fin"
                                    id="buscar_fecha_fin">
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button type="button" id="botom_genera_buscar"
                                            class="btn btn-success btn-sm w-100 mt-8 btn-icon"
                                            onclick="ajaxListado()"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="tabla_facturas">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@stop()
@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $(document).ready(function () {
            ajaxListado();
        });

        function ajaxListado() {

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
                url: "{{ route('factura.ajaxListadoFacturasCliente') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if (data.estado) {
                        $('#tabla_facturas').html(data.data.listado)
                    } else {

                    }
                    Swal.close();

                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    Swal.close();
                    Swal.fire('Error', xhr.responseText, 'error', 5000);
                }
            })
        }

        function modalAnularFactura(factura) {
            $('#factura_id').val(factura)
            $('#modalAnular').modal('show')
        }

        function anularFactura() {
            if ($("#formularioAnulaciion")[0].checkValidity()) {

                var boton = $("#boton_anular_factura");
                var iconoCarga = boton.find("i");

                boton.attr("disabled", true);
                iconoCarga.show();
                let datos = $('#formularioAnulaciion').serializeArray()
                $.ajax({
                    url: "{{ url('factura/anularFactura') }}",
                    method: "POST",
                    data: datos,
                    success: function (data) {

                        if (data.estado) {
                            Swal.fire({
                                icon: 'success',
                                title: "EXITO!",
                                text: "SE ANULO CON EXITO",
                            })
                            ajaxListado();
                            $('#modalAnular').modal('hide')
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: data.mensaje.descripcion.codigoDescripcion,
                                text: JSON.stringify(data.mensaje.descripcion.mensajesList),

                            })
                            $('#modalAnular').modal('hide')
                        }
                        boton.attr("disabled", false);
                        iconoCarga.hide();
                    }
                })

            } else {
                $("#formularioAnulaciion")[0].reportValidity();
            }
        }

        function modalRecepcionFacuraContingenciaFueraLinea() {
            $('#evento_significativo_contingencia_select').val('')
            $('#tablas_facturas_offline').hide('toggle');
            $('#modmodalContingenciaFueraLinea').modal('show')
        }

        function buscarEventosSignificativos() {
            if ($("#formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo")[0].checkValidity()) {
                let datos_formulario = $("#formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo")
                    .serializeArray();
                $.ajax({
                    url: "{{ url('eventoSignificativo/buscarEventosSignificativos') }}",
                    method: "POST",
                    data: datos_formulario,
                    success: function (data) {
                        $('#evento_significativo_contingencia_select').empty();
                        if (data.estado) {
                            $('#bloque_no_hay_eventos').hide('toggle');

                            var newOption = $('<option>').text("SELECCIONE").val(null);
                            $('#evento_significativo_contingencia_select').append(newOption);

                            $(data.data.eventos).each(function (index, element) {
                                var optionText = element.fecha_ini + " | " + element
                                    .fecha_fin + " | " + element.descripcion;
                                var optionValue = element.id;
                                var newOption = $('<option>').text(optionText).val(optionValue);
                                $('#evento_significativo_contingencia_select').append(newOption);
                            });
                        } else {
                            $('#mensaje_contingencia').text(data.mensaje)
                            $('#bloque_no_hay_eventos').show('toggle');
                        }
                    }
                })
            } else {
                $("#formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo")[0].reportValidity();
            }
        }

        function muestraTableFacturaPaquete() {
            let valor = $('#evento_significativo_contingencia_select').val();
            $.ajax({
                url: "{{ url('eventoSignificativo/muestraTableFacturaPaquete') }}",
                method: "POST",
                data: {
                    fecha: $('#fecha_contingencia').val(),
                    valor: $('#evento_significativo_contingencia_select').val()
                },
                dataType: 'json',
                success: function (data) {
                    if (data.estado) {
                        $('#tablas_facturas_offline').html(data.data.listado);
                        $('#tablas_facturas_offline').show('toggle');
                    } else { }
                }
            })
        }

        function mandarFacturasPaquete() {

            $('#boton_enviar_paquete').prop('disabled', true);
            let arraye = $('#formularioEnvioPaquete').serializeArray();
            arraye.push({
                name: 'evento_significativo_id',
                value: $('#evento_significativo_contingencia_select').val()
            });
            $.ajax({
                url: "{{ url('eventoSignificativo/mandarFacturasPaquete') }}",
                method: "POST",
                data: arraye,
                dataType: 'json',
                success: function (data) {
                    if (data.estado) {
                        ajaxListado();
                        $('#modmodalContingenciaFueraLinea').modal('hide')
                        Swal.fire({
                            icon: 'success',
                            title: JSON.stringify(data.mensaje),
                            showConfirmButton: false,

                            timerProgressBar: true
                        });
                        $('#boton_enviar_paquete').prop('disabled', false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: JSON.stringify(data.data),
                            showConfirmButton: false,

                            timerProgressBar: true
                        });
                    }

                    $('#boton_enviar_paquete').prop('disabled', false);
                }
            })
        }

        function desanularFacturaAnulado(factura) {
            Swal.fire({
                title: "Estas seguro de Revertir la Factura anulada?",
                text: "Esta accion no se podra revertir!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, estoy seguro!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('factura/desanularFacturaAnulado') }}",
                        method: "POST",
                        data: {
                            factura: factura
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.estado === "success") {
                                ajaxListado();
                                $('#modmodalContingenciaFueraLinea').modal('hide')
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
                        }
                    })
                }
            });
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

        function desanularFacturaAnulado(factura) {
            Swal.fire({
                title: "Estas seguro de Revertir la Factura anulada?",
                text: "Esta accion no se podra revertir!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, estoy seguro!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('factura/desanularFacturaAnulado') }}",
                        method: "POST",
                        data: {
                            factura: factura
                        },
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
                        }
                    })
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

        function imprimeREcibo(recibo) {
            href = "{{ url('factura/recibo') }}/" + recibo;

            window.open(href, '_blank');
        }

        {
            {

                function ajaxFacturaVenta(factura) {
                    $('#datosFacturacion').html('');
                    datos = {
                        factura_id: factura
                    }
                    $.ajax({
                        url: "{{ route('factura.ajaxFacturaVenta') }}",
                        method: "POST",
                        data: datos,
                        success: function (resultado) {
                            if (resultado.estado) {
                                $('#datosFacturacion').html(resultado.data.formularioFacturacion);

                                $('#datosFacturacion').find('[data-control="select2"]').select2({
                                    dropdownParent: $('#modalFacturacion')
                                });


                                $('#modalFacturacion').modal('show');
                            }
                        }
                    });
                }

                function cambiarUnidaMedida(detalle, select) {
                    datos = {
                        detalle: detalle,
                        unidad: select.value
                    }
                    $.ajax({
                        url: "{{ route('factura.cambiarUnidaMedida') }}",
                        method: "POST",
                        data: datos,
                        success: function (resultado) {
                            if (resultado.estado) {
                                $('#text_unidad_' + resultado.data.detalle).show('toggle');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error AJAX:', error);
                            console.error(xhr.responseText);
                            Swal.fire('Error de conexión', 'Ocurrió un problema al comunicarse con el servidor.',
                                'error');
                        }
                    });
                }

                function cambiarProductoServicio(detalle, select) {
                    let option = select.options[select.selectedIndex];

                    let idProducto = option.value;
                    let codigoProducto = option.dataset.codigo_producto;
                    let codigoActividad = option.dataset.codigo_actividad;

                    let datos = {
                        detalle: detalle,
                        idProducto: idProducto,
                        codigoProducto: codigoProducto,
                        codigoActividad: codigoActividad
                    }
                    $.ajax({
                        url: "{{ route('factura.cambiarProductoServicio') }}",
                        method: "POST",
                        data: datos,
                        success: function (resultado) {
                            if (resultado.estado) {
                                $('#text_producto_servicio_' + resultado.data.detalle).show('toggle');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error AJAX:', error);
                            console.error(xhr.responseText);
                            Swal.fire('Error de conexión', 'Ocurrió un problema al comunicarse con el servidor.',
                                'error');
                        }
                    });
                }

                function emitirFactura() {

                    if ($("#formularioFacturacion")[0].checkValidity()) {

                        let datos = $('#formularioFacturacion').serializeArray();

                        $.ajax({
                            url: "{{ route('factura.emitirFactura') }}",
                            method: "POST",
                            data: datos,
                            success: function (data) {
                                if (data.estado === "VALIDADA") {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Excelente!',
                                        text: 'LA FACTURA FUE VALIDADA',
                                        timer: 3000
                                    })
                                    if (data.numero != null && data.numero != '') {
                                        window.open("{{ url('factura/generaPdfFacturaNewCv') }}/" + data.numero,
                                            "_blank", "width=800,height=600");

                                        $('#modalFacturacion').modal('hide')
                                        ajaxListado();
                                    } else {
                                        window.location.href = "{{ url('factura/listado') }}"
                                    }
                                } else if (data.estado === "error_email") {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: data.text,
                                    })

                                    boton.attr("disabled", false);
                                    iconoCarga.hide();
                                } else if (data.estado === "OFFLINE") {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Exito!',
                                        text: 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
                                        timer: 2000
                                    })
                                    window.location.href = "{{ url('factura/listado') }}"
                                } else if (data.estado === "error_firma") {
                                    Swal.fire({
                                        icon: 'error',
                                        title: data.text,
                                        text: 'EMISION DE FACTURA RECHAZADO',
                                    })

                                    boton.attr("disabled", false);
                                    iconoCarga.hide();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: data.text + " " + data.data,
                                        text: 'LA FACTURA FUE RECHAZADA',
                                    })

                                    boton.attr("disabled", false);
                                    iconoCarga.hide();
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Error AJAX:', error);
                                console.error(xhr.responseText);
                                Swal.fire('Error de conexión', 'Ocurrió un problema al comunicarse con el servidor.',
                                    'error');
                            }
                        });

                    } else {
                        $("#formularioFacturacion")[0].reportValidity();
                    }
                }
                --}
        }

        function verificaNit() {
            if ($('#tipo_documento').val() === "5") {
                let nit = $('#nit_factura').val();
                $.ajax({
                    url: "{{ url('factura/verificarNit') }}",
                    data: {
                        nit: nit
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        if (data.estado) {
                            if (data.data.estadoSiat) {
                                $('#execpcion').prop('checked', false);
                                $('#nitsiexiste').show('toggle')
                                $('#nitnoexiste').hide('toggle')
                            } else {
                                $('#nitnoexiste').show('toggle')
                                $('#nitsiexiste').hide('toggle')
                                $('#execpcion').prop('checked', true);
                            }
                        } else {

                        }
                    }
                });

                $('#complemento').val(null)
                $('#bloque_complemento').hide('toggle')

            } else if ($('#tipo_documento').val() === "1") {

                $('#bloque_complemento').show('toggle')
                $('#nitnoexiste').hide('toggle')
                $('#nitsiexiste').hide('toggle')
                $('#errorValidar').hide('toggle')
                $('#execpcion').prop('checked', false);

            } else {
                $('#nitnoexiste').hide('toggle')
                $('#nitsiexiste').hide('toggle')
                $('#errorValidar').hide('toggle')
                $('#execpcion').prop('checked', false);

                $('#bloque_complemento').hide('toggle')

            }
        }

        function bloqueCAFC() {
            if ($('#tipo_facturacion').val() === "offline") {

                let tipo_documento = $('#tipo_documento').val();
                let emision = $('#tipo_facturacion').val();

                verificarExcepcion(tipo_documento, emision);

            } else {
                $('#numero_factura_cafc').val(null)
                $('#bloque_cafc, #numero_fac_cafc').hide('toggle')
                $('#execpcion').prop('checked', false);

                $('#select_cufd_vigentes').html('')
                $('#bloque_cufd_offline').hide('toggle');


                $('input[name="uso_cafc"][value="No"]').prop('checked', true);
            }
        }

        function verificarExcepcion(tipo_documento, emision, uso_cafse) {
            if (emision === "offline") {
                if (tipo_documento == "5") {
                    $('#execpcion').prop('checked', true);
                } else {
                    $('#execpcion').prop('checked', false);
                }
                $('#bloque_cafc').show('toggle')

                $.ajax({
                    url: "{{ url('eventoSignificativo/sacarCufdsPorTipoEvento') }}",
                    method: "POST",
                    data: {},
                    success: function (data) {
                        if (data.estado) {

                            $('#select_cufd_vigentes').html(data.data.select)
                            $('#bloque_cufd_offline').show('toggle');
                        } else {
                            $('#select_cufd_vigentes').html('')
                            $('#bloque_cufd_offline').hide('toggle');
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: data.text,
                            })
                        }
                    }
                })
            }
        }

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

    </script>
@endsection