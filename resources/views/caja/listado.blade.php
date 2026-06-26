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
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Caja</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="abrirCaja()">
                            <i class="fa fa-plus"></i> Abrir Caja
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
                url: "{{ route('caja.ajaxListado') }}",
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

        function abrirCaja() {
            Swal.fire({
                title: '<span class="fw-bold">APERTURA DE CAJA</span>',
                html: `
                                <div class="text-start">
                                    <label class="fw-semibold mb-2">
                                        Monto de apertura
                                    </label>
                                    <input type="number"
                                        id="monto_apertura"
                                        class="swal2-input"
                                        placeholder="Ingrese el monto"
                                        min="0"
                                        value="0">
                                </div>
                            `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: `
                                <i class="fa fa-unlock me-1"></i>
                                Abrir Caja
                            `,
                cancelButtonText: `
                                <i class="fa fa-times me-1"></i>
                                Cancelar
                            `,
                confirmButtonColor: '#009ef7',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                preConfirm: () => {
                    let monto = $('#monto_apertura').val();
                    if (monto === '' || monto < 0) {
                        Swal.showValidationMessage(
                            'Debe ingresar un monto válido'
                        );
                        return false;
                    }
                    return monto;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Abriendo caja...',
                        text: 'Espere un momento',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $.ajax({
                        url: "{{ route('caja.abrirCaja') }}",
                        method: "POST",
                        data: {
                            monto_apertura: result.value
                        },
                        success: function (resultado) {
                            Swal.close();
                            if (resultado.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Caja abierta',
                                    text: resultado.mensaje,
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                                ajaxListado();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: resultado.mensaje
                                });
                            }
                        },

                        error: function () {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error inesperado',
                                text: 'No se pudo abrir la caja'
                            });
                        }
                    });
                }
            });
        }

        function cerrarCaja(id) {
            Swal.fire({
                title: '<span class="fw-bold text-danger">CERRAR CAJA</span>',
                html: `
                            <div class="text-center">
                                <i class="fa fa-lock text-danger"
                                    style="font-size: 55px;"></i>
                                <p class="mt-3 mb-0 fs-6 text-gray-700">
                                    ¿Está seguro de cerrar esta caja?
                                </p>
                                <small class="text-muted">
                                    Esta acción registrará el cierre de caja actual.
                                </small>
                            </div>
                        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `
                            <i class="fa fa-lock me-1"></i>
                            Sí, cerrar
                        `,
                cancelButtonText: `
                            <i class="fa fa-times me-1"></i>
                            Cancelar
                        `,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Cerrando caja...',
                        text: 'Espere un momento',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $.ajax({
                        url: "{{ route('caja.cerrarCaja') }}",
                        method: "POST",
                        data: {
                            caja_id: id
                        },
                        success: function (resultado) {
                            Swal.close();
                            if (resultado.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Caja cerrada',
                                    text: resultado.mensaje,
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                                ajaxListado();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: resultado.mensaje
                                });
                            }
                        },
                        error: function () {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error inesperado',
                                text: 'No se pudo cerrar la caja'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection