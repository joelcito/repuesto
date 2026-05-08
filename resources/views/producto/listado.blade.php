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


<!-- MODAL PRODUCTO -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="fw-bold">FORMULARIO DE PRODUCTO <span class="text-info" id="nombre_busqueda"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body scroll-y">
                <form id="formularioProducto">

                    <input type="hidden" name="id" id="id" value="0">

                    <div class="row">

                        <div class="col-md-3">
                            <label>Código Barras</label>
                            <input type="text" class="form-control form-control-sm" name="codigo_barras"
                                id="codigo_barras">
                        </div>

                        <div class="col-md-3">
                            <label>Código Interno</label>
                            <input type="text" class="form-control form-control-sm" name="codigo_interno"
                                id="codigo_interno">
                        </div>

                        <div class="col-md-3">
                            <label>Nombre</label>
                            <input type="text" class="form-control form-control-sm" name="nombre" id="nombre">
                        </div>

                        <div class="col-md-3">
                            <label>Categoría</label>
                            <input type="text" class="form-control form-control-sm" name="categoria" id="categoria">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Marca</label>
                            <input type="text" class="form-control form-control-sm" name="marca" id="marca">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Stock Actual</label>
                            <input type="number" class="form-control form-control-sm" name="stock_actual"
                                id="stock_actual">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Stock Mínimo</label>
                            <input type="number" class="form-control form-control-sm" name="stock_minimo"
                                id="stock_minimo">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Unidad</label>
                            <input type="text" class="form-control form-control-sm" name="unidad" id="unidad">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Precio Compra</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="precio_compra"
                                id="precio_compra">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Precio Venta</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="precio_venta"
                                id="precio_venta">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Precio Mayor</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="precio_mayor"
                                id="precio_mayor">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Ubicación</label>
                            <input type="text" class="form-control form-control-sm" name="ubicacion" id="ubicacion">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Proveedor</label>
                            <input type="text" class="form-control form-control-sm" name="proveedor" id="proveedor">
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Descripción</label>
                            <textarea class="form-control form-control-sm" name="descripcion"
                                id="descripcion"></textarea>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success w-100" onclick="guardarProducto()">Guardar Producto</button>
            </div>

        </div>
    </div>
</div>


<!-- LISTADO -->
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">

            <div class="card shadow-sm">

                <div class="card-header bg-light-info py-4 d-flex justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Productos</h3>

                    <button class="btn btn-primary btn-sm" onclick="modalNuevoProducto()">
                        <i class="fa fa-plus"></i> Nuevo Producto
                    </button>
                </div>

                <div class="card-body py-4" id="table_listado">
                    <!-- AJAX -->
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
        });

        $(document).ready(function () {
            ajaxListado();
        });

        // LISTADO
        function ajaxListado() {
            $.ajax({
                url: "{{ route('producto.ajaxListado') }}",
                method: "POST",
                success: function (resultado) {
                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado);
                    }
                }
            });
        }

        // NUEVO
        function modalNuevoProducto() {
            $('#formularioProducto')[0].reset();
            $('#id').val(0);
            $('#modalProducto').modal('show');
        }

        // GUARDAR
        function guardarProducto() {

            let datos = $('#formularioProducto').serializeArray();

            $.ajax({
                url: "{{ route('producto.guardarProducto') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {

                    if (resultado.estado) {

                        Swal.fire({
                            icon: "success",
                            title: "Producto guardado",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        ajaxListado();
                        $('#modalProducto').modal('hide');
                    }
                }
            });
        }

        // EDITAR
        function editarProducto(producto) {

            $('#id').val(producto.id);
            $('#codigo_barras').val(producto.codigo_barras);
            $('#codigo_interno').val(producto.codigo_interno);
            $('#nombre').val(producto.nombre);
            $('#categoria').val(producto.categoria);
            $('#marca').val(producto.marca);
            $('#stock_actual').val(producto.stock_actual);
            $('#stock_minimo').val(producto.stock_minimo);
            $('#unidad').val(producto.unidad);
            $('#precio_compra').val(producto.precio_compra);
            $('#precio_venta').val(producto.precio_venta);
            $('#precio_mayor').val(producto.precio_mayor);
            $('#ubicacion').val(producto.ubicacion);
            $('#proveedor').val(producto.proveedor);
            $('#descripcion').val(producto.descripcion);

            $('#modalProducto').modal('show');
        }

        // ELIMINAR
        function eliminarProducto(id, nombre) {

            Swal.fire({
                title: "Eliminar " + nombre + "?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí eliminar"
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('producto.eliminarProducto') }}",
                        method: "POST",
                        data: { producto: id },
                        success: function () {

                            Swal.fire("Eliminado", "", "success");
                            ajaxListado();
                        }
                    });

                }
            });
        }
    </script>
@endsection