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


<div class="modal fade" id="modalProveedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">FORMULARIO DE PROVEEDOR <span class="text-info" id="nombre_busqueda"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioProveedor">
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nombre Completo</label>
                                <input type="text" class="form-control form-control-sm" id="nombre_completo"
                                    name="nombre_completo">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">NIT</label>
                                <input type="text" class="form-control form-control-sm" id="nit" name="nit"
                                    maxlength="13">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Razon Social</label>
                                <input type="text" class="form-control form-control-sm" id="razon_social"
                                    name="razon_social">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Celular</label>
                                <input type="text" class="form-control form-control-sm" id="celular" name="celular"
                                    maxlength="8">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Direccion</label>
                                <textarea class="form-control form-control-sm" id="direccion"
                                    name="direccion"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="guardarProveedor()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Proveedores</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevoProveedor()">
                            <i class="fa fa-plus"></i> Nuevo Proveedor
                        </button>
                    </div>
                </div>

                <div class="card-body py-4" id="table_listado">

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
            ajaxListado();
        });


        function ajaxListado() {
            let datos = {};
            $.ajax({
                url: "{{ route('proveedor.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {

                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

                    }

                }
            })
        }

        function modalNuevoProveedor() {
            $('#celular').val('')
            $('#direccion').val('')
            $('#razon_social').val('')
            $('#nit').val('')
            $('#nombre_completo').val('')
            $('#id').val(0)
            $('#modalProveedor').modal('show')
        }

        function guardarProveedor() {
            let datos = $('#formularioProveedor').serializeArray();

            $.ajax({
                url: "{{ route('proveedor.guardarProveedor') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 3000,
                            showConfirmButton: false
                        });
                        ajaxListado();
                        $('#modalProveedor').modal('hide');
                    } else {

                    }
                },
                error: function (xhr) {
                    limpiarErorres();

                    if (xhr.status === 422) {
                        let errores = xhr.responseJSON.errors;

                        for (let campo in errores) {
                            let mensaje = errores[campo][0];

                            let input = $(`[name="${campo}"]`);
                            input.addClass("is-invalid");
                            input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                }
            });
        }

        function editarProveedor(proveedor) {

            $('#celular').val(proveedor.celular)
            $('#direccion').val(proveedor.direccion)
            $('#razon_social').val(proveedor.razon_social)
            $('#nit').val(proveedor.nit)
            $('#nombre_completo').val(proveedor.nombre_completo)
            $('#id').val(proveedor.id)
            $('#modalProveedor').modal('show')

        }

        function eliminarProveedor(proveedor, razon_social) {
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
                        url: "{{ route('proveedor.eliminarProveedor') }}",
                        method: "POST",
                        data: { proveedor: proveedor },
                        success: function (resultado) {
                            if (resultado.estado) {
                                ajaxListado();
                                Swal.fire(
                                    'Eliminado!',
                                    'La proveedor ha sido eliminado correctamente.',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error',
                                    resultado.message || 'No se pudo eliminar la proveedor.',
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