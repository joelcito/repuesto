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
<div class="modal fade" id="modalIncorporacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-light-primary">
                <h3 class="fw-bold">
                    <i class="fa fa-box-open me-2"></i>
                    INCORPORACIÓN DE PRODUCTO
                </h3>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="formularioIncorporacion">

                    <div class="row">

                        <div class="col-md-6">
                            <label class="fw-semibold fs-6 mb-2">
                                Nombre Producto
                            </label>

                            <input type="text" class="form-control form-control-sm" name="nombre_producto">


                        </div>



                    </div>



                    <div class="row mt-3">

                        <div class="col-md-12">

                            <label class="fw-semibold fs-6 mb-2">
                                Descripción Producto
                            </label>

                            <textarea class="form-control form-control-sm" rows="3"
                                name="descripcion_producto"></textarea>

                        </div>

                    </div>

                </form>

            </div>

            <div class="modal-footer">

                <button class="btn btn-light" data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button class="btn btn-primary" onclick="guardarIncorporacion()">

                    <i class="fa fa-save me-1"></i>
                    Guardar Incorporación

                </button>

            </div>

        </div>
    </div>
</div>
<!--end::Modal - Add task-->


<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Incorporaciones</h3>
                    <div class="card-toolbar">
                        <button class="btn btn-primary btn-sm" onclick="modalNuevaIncorporacion()">

                            <i class="fa fa-plus"></i>
                            Nueva Incorporación

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
                url: "{{ route('incorporacion.ajaxListado') }}",
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


        function guardarIncorporacion() {

            let datos = $('#formularioIncorporacion')
                .serialize();

            $.ajax({

                url: "{{ route('incorporacion.guardarIncorporacion') }}",

                method: "POST",

                data: datos,

                success: function (resultado) {

                    if (resultado.estado) {

                        Swal.fire({

                            icon: 'success',

                            title: resultado.mensaje,

                            timer: 2000,

                            showConfirmButton: false

                        });

                        $('#modalIncorporacion').modal('hide');

                        ajaxListado();

                    } else {

                        Swal.fire({

                            icon: 'error',

                            title: resultado.mensaje

                        });

                    }

                }

            });

        }

        function modalNuevaIncorporacion() {

            $('#formularioIncorporacion')[0].reset();

            $('#modalIncorporacion').modal('show');

        }

        function eliminarIncorporacion(id) {

            Swal.fire({
                title: '¿Eliminar registro?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({

                        url: "{{ route('incorporacion.eliminarIncorporacion') }}",

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

                        }

                    });

                }

            });

        }
    </script>
@endsection