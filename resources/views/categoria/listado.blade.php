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
<div class="modal fade" id="modalRol" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">FORMULARIO DE CATEGORIA <span class="text-info" id="nombre_busqueda"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioRol">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                <input type="text" class="form-control form-control-sm" id="nombre" name="nombre">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Tipo</label>
                                <select class="form-select form-select-sm" name="tipo" id="tipo">
                                    <option value="AUTOMOVIL">AUTOMOVIL</option>
                                    <option value="MOTOCICLETA">MOTOCICLETA</option>
                                    <option value="MOTOCICLETA">INGRESO</option>
                                    <option value="MOTOCICLETA">SALIDA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Descripcion</label>
                                <input type="text" class="form-control form-control-sm" id="descripcion"
                                    name="descripcion">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="fw-semibold fs-6 mb-2">Categoría Padre</label>
                            <select class="form-select form-select-sm" name="parent_id" id="parent_id">
                                <option value="">-- Categoría Principal --</option>

                                @foreach($categoriasPadre as $cat)
                                    <option value="{{ $cat->id }}">
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="guardarRol()">Guardar</button>
                    </div>
                </div>
            </div>
            <!--end::Modal body-->
        </div>
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Add task-->

<!--begin::Content wrapper-->



<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Categoria</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevoRol()">
                            <i class="fa fa-plus"></i> Nuevo Categoria
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
                url: "{{ route('categoria.ajaxListado') }}",
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

        function limpiarErorres() {
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function modalNuevoRol() {
            limpiarErorres();

            $('#id').val(0)
            $('#nombre').val('')
            $('#descripcion').val('')
            $('#tipo').val('AUTOMOVIL')
            $('#parent_id').val('')

            $('#modalRol').modal('show')
        }

        function guardarRol() {
            let datos = $('#formularioRol').serializeArray();
            $.ajax({
                url: "{{ route('categoria.guardar') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 3000, // Se cierra en 3 segundos
                            showConfirmButton: false
                        });
                        ajaxListado();
                        $('#modalRol').modal('hide')
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

        function editarRol(rol) {
            limpiarErorres();

            $('#id').val(rol.id)
            $('#nombre').val(rol.nombre)
            $('#descripcion').val(rol.descripcion)
            $('#tipo').val(rol.tipo)
            $('#parent_id').val(rol.parent_id) // 👈 CLAVE

            $('#modalRol').modal('show')
        }

        function eliminarRol(rol) {
            Swal.fire({
                title: "Quieres eliminar " + rol.nombre,
                text: "Ya no podras recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Si, borrar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('categoria.eliminar') }}",
                        method: "POST",
                        data: rol,
                        success: function (resultado) {
                            if (resultado.estado) {
                                ajaxListado();
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.',

                            });
                        }
                    });
                } else if (result.dismiss === "cancel") {
                    Swal.fire(
                        "Cancelado",
                        "La operacion fue cancelada",
                        "error"
                    )
                }
            });

        }

    </script>
@endsection