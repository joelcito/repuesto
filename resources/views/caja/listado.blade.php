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
                    <h3 class="card-title fw-bold">Listado de caja</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="abrirCaja()">
                            <i class="fa fa-plus"></i> Abrir Caja
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
                url: "{{ route('caja.ajaxListado') }}",
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

        function abrirCaja() {

            Swal.fire({

                title: 'Monto apertura',

                input: 'number',

                inputValue: 0,

                showCancelButton: true,

                confirmButtonText: 'Abrir Caja'

            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({

                        url: "{{ route('caja.abrirCaja') }}",

                        method: "POST",

                        data: {
                            monto_apertura: result.value
                        },

                        success: function (resultado) {

                            if (resultado.estado) {

                                Swal.fire(
                                    'Correcto',
                                    resultado.mensaje,
                                    'success'
                                );

                                ajaxListado();

                            } else {

                                Swal.fire(
                                    'Error',
                                    resultado.mensaje,
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        }

        function cerrarCaja(id) {

            Swal.fire({

                title: '¿Cerrar caja?',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Cerrar'

            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({

                        url: "{{ route('caja.cerrarCaja') }}",

                        method: "POST",

                        data: {
                            caja_id: id
                        },

                        success: function (resultado) {

                            if (resultado.estado) {

                                Swal.fire(
                                    'Correcto',
                                    resultado.mensaje,
                                    'success'
                                );

                                ajaxListado();

                            } else {

                                Swal.fire(
                                    'Error',
                                    resultado.mensaje,
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        }



    </script>
@endsection