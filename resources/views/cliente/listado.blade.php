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

<!--begin::Modal - Add task-->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">FORMULARIO DE CLIENTE <span class="text-info" id="nombre_busqueda"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioCliente" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="required fw-semibold fs-6 mb-2">Nombres</label>
                            <input type="text" class="form-control form-control-sm" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label class="required fw-semibold fs-6 mb-2">Apellido paterno</label>
                            <input type="text" class="form-control form-control-sm" id="ap_paterno" name="ap_paterno"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-semibold fs-6 mb-2">Apellido materno</label>
                            <input type="text" class="form-control form-control-sm" id="ap_materno" name="ap_materno">
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-3">
                            <label class="fw-semibold fs-6 mb-2">Celular</label>
                            <input type="text" class="form-control form-control-sm" id="celular" name="celular"
                                maxlength="8">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-semibold fs-6 mb-2">Cedula Identidad</label>
                            <input type="text" class="form-control form-control-sm" id="cedula" name="cedula"
                                maxlength="10">
                        </div>
                        <div class="col-md-3">

                            <label class="fw-semibold fs-6 mb-2">NIT</label>
                            <input type="text" class="form-control form-control-sm" id="nit" name="nit">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-semibold fs-6 mb-2">Razon Social</label>
                            <input type="text" class="form-control form-control-sm" id="razon_social"
                                name="razon_social">
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="fw-semibold fs-6 mb-2">Direccion</label>
                        <textarea class="form-control form-control-sm" id="direccion" name="direccion"></textarea>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">Imagen Cliente</label>
                        <input type="file" accept="image/*" class="form-control form-control-sm" id="imagen"
                            name="imagen">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="fw-semibold fs-6 mb-2">Imagen Cedula Identidad Anverso</label>
                            <input type="file" accept="image/*" class="form-control form-control-sm"
                                id="imagen_CI_anverso" name="imagen_CI_anverso">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold fs-6 mb-2">Imagen Cedula Identidad Reverso</label>
                            <input type="file" accept="image/*" class="form-control form-control-sm"
                                id="imagen_CI_reverso" name="imagen_CI_reverso">
                        </div>
                    </div>
                    <div class="row mb-7 mt-7">
                        <label class="fw-semibold fs-6 mb-2">REFERENCIAS:</label>
                        <div class="col-md-4">
                            <label class="fw-semibold fs-6 mb-2">Nombre Completo</label>
                            <input type="text" class="form-control form-control-sm" id="nombre_referencia_1"
                                name="nombre_referencia_1">

                            <label class="fw-semibold fs-6 mb-2">Celular</label>
                            <input type="text" class="form-control form-control-sm" id="celular_referencia_1"
                                name="celular_referencia_1" maxlength="8">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-semibold fs-6 mb-2">Nombre Completo</label>
                            <input type="text" class="form-control form-control-sm" id="nombre_referencia_2"
                                name="nombre_referencia_2">

                            <label class="fw-semibold fs-6 mb-2">Celular</label>
                            <input type="text" class="form-control form-control-sm" id="celular_referencia_2"
                                name="celular_referencia_2" maxlength="8">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-semibold fs-6 mb-2">Nombre Completo</label>
                            <input type="text" class="form-control form-control-sm" id="nombre_referencia_3"
                                name="nombre_referencia_3">

                            <label class="fw-semibold fs-6 mb-2">Celular</label>
                            <input type="text" class="form-control form-control-sm" id="celular_referencia_3"
                                name="celular_referencia_3" maxlength="8">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="guardarCliente()">Guardar</button>
                    </div>
                </div>
            </div>
            <!--end::Modal body-->
        </div>
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Add task-->


<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Cliente</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevoCliente()">
                            <i class="fa fa-plus"></i> Nuevo Cliente
                        </button>
                    </div>
                </div>

                <div class="card-body py-4" id="table_listado">
                    <!-- El listado se carga por AJAX -->
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
            // definimos cabecera donde estarra el token y poder hacer nuestras operaciones de put,post...
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $(document).ready(function () {
            ajaxListado();
        });


        function ajaxListado() {
            let datos = {};
            $.ajax({
                url: "{{ route('cliente.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {

                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

                    }
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    // Swal.close();
                }
            })
        }

        function modalNuevoCliente() {
            $('#celular_referencia_3').val('')
            $('#nombre_referencia_3').val('')
            $('#celular_referencia_2').val('')
            $('#nombre_referencia_2').val('')
            $('#celular_referencia_1').val('')
            $('#nombre_referencia_1').val('')
            $('#direccion').val('')
            $('#razon_social').val('')
            $('#nit').val('')
            $('#cedula').val('')
            $('#celular').val('')
            $('#ap_materno').val('')
            $('#ap_paterno').val('')
            $('#nombre').val('')
            $('#id').val(0)
            // $('#password').attr('required', true)
            $('#modalCliente').modal('show')
        }

        function guardarCliente() {

            if ($("#formularioCliente")[0].checkValidity()) {
                let form = document.getElementById('formularioCliente');
                let datos = new FormData(form);  // <-- Importante

                $.ajax({
                    url: "{{ route('cliente.guardarCliente') }}",
                    method: "POST",
                    data: datos,
                    processData: false, // <-- Obligatorio para FormData
                    contentType: false, // <-- Obligatorio para FormData
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "EL REGISTRO FUE EXITOSO.",
                                icon: "success",
                                timer: 3000, // Se cierra en 3 segundos
                                showConfirmButton: false
                            });
                            ajaxListado();
                            $('#modalCliente').modal('hide');
                        } else {

                        }
                    },
                    error: function (xhr) {
                        // limpiarErorres();

                        if (xhr.status === 422) {
                            let errores = xhr.responseJSON.errors;

                            for (let campo in errores) {
                                let mensaje = errores[campo][0];

                                let input = $(`[name="${campo}"]`);
                                input.addClass("is-invalid");
                                input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                            }

                            // Swal.fire({
                            //     icon: 'error',
                            //     title: 'Error',
                            //     text: JSON.strify(xhr.responseJSON),
                            // });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.',
                            });
                        }
                    }
                });
            } else {
                $("#formularioCliente")[0].reportValidity();
            }

        }

        function editarCliente(cliente) {
            $('#celular_referencia_3').val(cliente.celular_referencia_3)
            $('#nombre_referencia_3').val(cliente.nombre_referencia_3)
            $('#celular_referencia_2').val(cliente.celular_referencia_2)
            $('#nombre_referencia_2').val(cliente.nombre_referencia_2)
            $('#celular_referencia_1').val(cliente.celular_referencia_1)
            $('#nombre_referencia_1').val(cliente.nombre_referencia_1)
            $('#direccion').val(cliente.direccion)
            $('#razon_social').val(cliente.razon_social)
            $('#nit').val(cliente.nit)
            $('#cedula').val(cliente.cedula)
            $('#celular').val(cliente.celular)
            $('#ap_materno').val(cliente.ap_materno)
            $('#ap_paterno').val(cliente.ap_paterno)
            $('#nombre').val(cliente.nombres)
            $('#rol_id').val(cliente.rol_id)
            $('#id').val(cliente.id)
            $('#modalCliente').modal('show')
        }

        function eliminarCliente(cliente, razon_social) {
            Swal.fire({
                title: "¿Quieres eliminar " + razon_social + "?",
                text: "¡No podrás recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Sí, borrar",
                cancelButtonText: "No, cancelar",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cliente.eliminarCliente') }}",
                        method: "POST",
                        data: {
                            cliente: cliente
                        },
                        success: function (resultado) {
                            if (resultado.estado) {
                                ajaxListado(); // recarga el listado
                                Swal.fire(
                                    'Eliminado!',
                                    'El usuario ha sido eliminado correctamente.',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error',
                                    resultado.message || 'No se pudo eliminar el usuario.',
                                    'error'
                                );
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.'
                            });
                        }
                    });


                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'La operación fue cancelada',
                        'info'
                    );
                }
            });
        }
    </script>
@endsection