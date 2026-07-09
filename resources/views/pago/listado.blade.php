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
<div class="modal fade" id="modalIngreso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Formulario de <span id="text_tipoo_modal" class="text-info"></span></h2>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioIngresoSalida">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Categoria</label>
                                <select class="form-select form-select-sm" name="categoria_id" id="categoria_id"
                                    onchange="sacarSubCategorias()"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">SubCategoria</label>
                                <select class="form-select form-select-sm" name="subcategoria_id"
                                    id="subcategoria_id"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Monto</label>
                                <input type="number" id="monto" name="monto"
                                    class="form-control form-control-solid mb-3 mb-lg-0" min="0.1" step="0.01" value="0"
                                    required>
                                <input type="hidden" id="tipo" name="tipo" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                <input type="text" id="descripcion" name="descripcion"
                                    class="form-control form-control-solid mb-3 mb-lg-0">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success w-100" onclick="guardarTipoIngresoSalida()">Guardar</button>
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
                <div class="card-header flex-wrap bg-light-info py-4">
                    <h3
                        class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        LISTADO DE PAGOS</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm fw-bold btn-success ml-5"
                            onclick="modalIngresoSalida('INGRESO')"><i class="fas fa-money-bill"></i><i
                                class="fas fa-arrow-down"></i>Nuevo Ingreso</button>
                        <button type="button" class="btn btn-sm fw-bold btn-danger ml-5 m-3"
                            onclick="modalIngresoSalida('SALIDA')"><i class="fas fa-money-bill"></i><i
                                class="fas fa-arrow-up"></i>Nuevo Salida</button>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form id="formulario_busqueda">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Sucursal</label>
                                    <select data-control="select2" data-placeholder="Seleccione"
                                        class="form-select form-select-solid fw-bold"
                                        class="form-control form-control-sm" name="sucursal_id" id="sucursal_id"
                                        onchange="filtrarCajasScurusal()">
                                        <option value="">SELECCIONE</option>
                                        @foreach ($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Ini</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha_ini"
                                        name="fecha_ini" value="{{ $fechaIni }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha_fin"
                                        name="fecha_fin" value="{{ $fechaFin }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Usuario</label>
                                    <select data-control="select2" data-placeholder="Seleccione"
                                        class="form-select form-select-solid fw-bold"
                                        class="form-control form-control-sm" name="usuario_busqueda_id"
                                        id="usuario_busqueda_id" onchange="filtarCajas()">
                                        <option value="">SELECCIONE</option>
                                        @foreach ($usuarios as $user)
                                            <option data-sucursal="{{ $user->sucursal?->id }}" value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button onclick="ajaxListado()" type="button"
                                    class="btn btn-sm w-100 btn-success mt-8"><i class="fa fa-search"></i></button>
                            </div>
                            <div class="col-md-1">
                                <button onclick="generaExcelPago()" type="button"
                                    class="btn btn-sm w-100 btn-success mt-8" title="Genera Reporte"><i
                                        class="fa fa-file-excel"></i></button>
                            </div>
                        </div>
                    </form>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_todos">
                                Todos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_repuestos">
                                Repuestos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_lubricantes">
                                Lubricantes
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">

                        <div class="tab-pane fade show active" id="tab_todos">
                            <div id="table_listado"></div>
                        </div>

                        <div class="tab-pane fade" id="tab_repuestos">
                            <div id="table_repuestos"></div>
                        </div>

                        <div class="tab-pane fade" id="tab_lubricantes">
                            <div id="table_lubricantes"></div>
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
            let datos = $('#formulario_busqueda').serializeArray();
            $.ajax({
                url: "{{ url('pago/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado) {

                        $('#table_listado').html(resultado.data.todos);
                        $('#table_repuestos').html(resultado.data.repuestos);
                        $('#table_lubricantes').html(resultado.data.lubricantes);

                    } else {

                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                    });
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
            $('#modalRol').modal('show')
        }

        function modalIngresoSalida(tipo) {
            $('#tipo').val(tipo)
            $('#text_tipoo_modal').text(tipo)
            $('#monto').val(0)
            $('#descripcion').val('')
            let categoriasIngreso = (tipo === "INGRESO") ? @json($categoriasIngreso) : @json($categoriasSalida);
            $('#categoria_id').empty();
            $('#categoria_id').append('<option value="">Seleccione</option>');
            $('#subcategoria_id').empty();
            $('#subcategoria_id').append('<option value="">Seleccione</option>');
            categoriasIngreso.forEach(c => {
                $('#categoria_id').append(
                    `<option value="${c.id}">${c.nombre}</option>`
                );
            })

            $('#modalIngreso').modal('show')
        }

        function sacarSubCategorias() {

            let categoria_id = $('#categoria_id').val();
            let subCategorias = @json($subCategorias);
            let subCategoriasFiltrados = subCategorias.filter(
                s => s.parent_id == categoria_id
            );

            $('#subcategoria_id').empty();
            $('#subcategoria_id').append('<option value="">Seleccione</option>');
            subCategoriasFiltrados.forEach(c => {
                $('#subcategoria_id').append(
                    `<option value="${c.id}">${c.nombre}</option>`
                );
            })

        }

        function guardarTipoIngresoSalida() {
            if ($("#formularioIngresoSalida")[0].checkValidity()) {
                datos = $("#formularioIngresoSalida").serializeArray()
                $.ajax({
                    url: "{{ url('pago/guardarTipoIngresoSalida') }}",
                    data: datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        if (data.estado) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Se registro con exito',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            $('#modalIngreso').modal('hide')
                            ajaxListado();
                        }
                    }
                });
            } else {
                $("#formularioIngresoSalida")[0].reportValidity()
            }
        }

        function ajaxDescargarReportePago() {
            let datos = $('#formulario_busqueda').serializeArray();
            $.ajax({
                url: "{{ url('pago/ajaxDescargarReportePago') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    const link = document.createElement('a');
                    link.href = 'data:application/pdf;base64,' + resultado.data.base64;
                    link.download = resultado.data.nombre;
                    link.click();
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                    });
                }
            })
        }

        function modalCerrarCaja() {
            $('#modalCerrarCaja').modal('show')
        }

        function modalAperturaCaja() {
            $('#monto_apertura').val(0)
            $('#descripcion').val('')
            $('#modalAperturaCaja').modal('show')
        }

        function guardarCerrarCaja() {
            if ($('#formularioCerrarCaja')[0].checkValidity()) {
                $('#boton_cerrar_caja').attr('disabled', true);
                let datos = $('#formularioCerrarCaja').serializeArray();
                $.ajax({
                    url: "{{ url('caja/guardarCerrarCaja') }}",
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

                            location.reload();
                        } else {

                        }
                    },
                    error: function (xhr) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.' + xhr,
                        });
                    }
                });
            } else {
                $('#formularioCerrarCaja')[0].reportValidity()
            }
        }

        function guardarAperturaCaja() {
            if ($('#formularioAperturaCaja')[0].checkValidity()) {
                $('#boton_abrir_caja').attr('disabled', true);
                let datos = $('#formularioAperturaCaja').serializeArray();
                $.ajax({
                    url: "{{ url('caja/guardarAperturaCaja') }}",
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

                            location.reload();
                        } else {

                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.' + xhr,
                        });
                    }
                });
            } else {
                $('#formularioAperturaCaja')[0].reportValidity()
            }
        }

        function filtarCajas() {
            let cajas = [];
            let usuarioOption = $('#usuario_busqueda_id option:selected');
            let sucursalId = usuarioOption.data('sucursal');
            $('#caja_id').empty();
            $('#caja_id').append('<option value="">SELECCIONE</option>');
            if (!sucursalId) {
                $('#caja_id').trigger('change');
                return;
            }
            let filtradas = cajas.filter(c => c.sucursal_id == sucursalId);
            filtradas.forEach(c => {
                let texto = "[" + c.estado + "] | " + c.fecha_apertura + " - " + c.fecha_cierre;
                $('#caja_id').append(`<option value="${c.id}">${texto}</option>`);
            });
            $('#caja_id').trigger('change');
        }

        function filtrarCajasScurusal() {
            let cajas = [];
            let sucursal_id = $('#sucursal_id').val()
            $('#caja_id').empty();
            $('#caja_id').append('<option value="">SELECCIONE</option>');
            if (!sucursal_id) {
                $('#caja_id').trigger('change');
                return;
            }
            let filtradas = cajas.filter(c => c.sucursal_id == sucursal_id);
            filtradas.forEach(c => {
                let texto = "[" + c.estado + "] | " + c.fecha_apertura + " - " + c.fecha_cierre;
                $('#caja_id').append(`<option value="${c.id}">${texto}</option>`);
            });
            $('#caja_id').trigger('change');
        }

        function generaExcelPago() {
            let datos = $('#formulario_busqueda').serializeArray();
            Swal.fire({
                title: 'Generando Excel...',
                text: 'Por favor espera mientras generamos el archivo.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('pago/generaExcelPago') }}",
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
                    link.download = 'reporte_pagos.xlsx';
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

    </script>
@endsection