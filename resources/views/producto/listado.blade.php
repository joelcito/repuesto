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

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Código Barras</label>

                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" name="codigo_barras"
                                    id="codigo_barras">

                                <button type="button" class="btn btn-primary btn-sm" onclick="generarCodigoBarras()">
                                    Generar
                                </button>
                            </div>




                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Código Interno</label>
                            <input type="text" class="form-control form-control-sm" name="codigo_interno"
                                id="codigo_interno">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" class="form-control form-control-sm" name="nombre" id="nombre">
                        </div>

                        <!-- VEHICULO -->
                        <!-- <div class="col-md-4">
                            <label class="form-label fw-bold">Vehículo</label>
                            <input type="text" class="form-control form-control-sm" name="vehiculo" id="vehiculo">
                        </div> -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Descripción</label>
                            <input class="form-control form-control-sm" name="descripcion" id="descripcion">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoría</label>

                            <select class="form-select form-select-sm" name="categoria_id" id="categoria_id">

                                <option value="">Seleccione</option>

                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca</label>

                            <select class="form-select form-select-sm" name="marca" id="marca">

                                <option value="">Seleccione</option>

                                @foreach($marcas as $marca)
                                    <option value="{{ $marca }}">
                                        {{ $marca }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">N° Parte</label>
                            <input type="text" class="form-control form-control-sm" name="numero_parte_vehiculo"
                                id="numero_parte_vehiculo">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Unidad</label>

                            <select class="form-select form-select-sm" name="unidad" id="unidad">

                                <option value="pieza">Pieza</option>
                                <option value="caja">Caja</option>
                                <option value="juego">Juego</option>

                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Stock Actual</label>
                            <input type="number" class="form-control form-control-sm" name="stock_actual"
                                id="stock_actual">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Stock Mínimo</label>
                            <input type="number" class="form-control form-control-sm" name="stock_minimo"
                                id="stock_minimo">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ubicación</label>

                            <select class="form-select form-select-sm" name="sucursal_id" id="sucursal_id">

                                <option value="">Seleccione</option>

                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Precio Compra</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="precio_compra"
                                id="precio_compra">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Precio Venta</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="precio_venta"
                                id="precio_venta">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Precio Mayor</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="precio_mayor"
                                id="precio_mayor">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Proveedor</label>

                            <select class="form-select form-select-sm" name="proveedor_id" id="proveedor_id">

                                <option value="">Seleccione</option>

                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">
                                        {{ $proveedor->nombre_completo }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Observaciones</label>
                            <input type="text" class="form-control form-control-sm" name="observaciones"
                                id="observaciones">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Imagen</label>

                            <input type="file" class="form-control form-control-sm" name="imagen" id="imagen"
                                accept="image/*">

                        </div>

                        <div class="col-md-4 text-center">

                            <img id="preview_imagen" src="{{ asset('imagenes/productos/default.jpg') }}" width="120"
                                class="img-thumbnail">

                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado</label>

                            <select class="form-select form-select-sm" name="estado" id="estado">

                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>

                            </select>
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

        function modalNuevoProducto() {

            $('#formularioProducto')[0].reset();

            $('#id').val(0);

            $('#preview_imagen').attr(
                'src',
                '/imagenes/productos/default.jpg'
            );

            $('#estado').val(1);

            $('#modalProducto').modal('show');
        }


        $('#imagen').change(function (e) {

            let reader = new FileReader();

            reader.onload = function (e) {
                $('#preview_imagen').attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);

        });

        function generarCodigoBarras() {

            let random = Date.now(); // base única

            let codigo = "REP-" + random.toString().slice(-8);

            $('#codigo_barras').val(codigo);
        }


        function generarCodigoBarras() {

            $.ajax({
                url: "/producto/generar-codigo",
                method: "POST",
                success: function (codigo) {
                    $('#codigo_barras').val(codigo);
                }
            });

        }

        function guardarProducto() {

            let formData = new FormData();

            formData.append('id', $('#id').val());

            formData.append('codigo_barras', $('#codigo_barras').val());
            formData.append('codigo_interno', $('#codigo_interno').val());
            formData.append('nombre', $('#nombre').val());
            formData.append('descripcion', $('#descripcion').val());
            formData.append('categoria_id', $('#categoria_id').val());
            formData.append('marca', $('#marca').val());
            formData.append('numero_parte_vehiculo', $('#numero_parte_vehiculo').val());
            formData.append('stock_actual', $('#stock_actual').val());
            formData.append('stock_minimo', $('#stock_minimo').val());
            formData.append('unidad', $('#unidad').val());
            formData.append('precio_compra', $('#precio_compra').val());
            formData.append('precio_venta', $('#precio_venta').val());
            formData.append('precio_mayor', $('#precio_mayor').val());
            formData.append('sucursal_id', $('#sucursal_id').val());
            formData.append('proveedor_id', $('#proveedor_id').val());
            formData.append('observaciones', $('#observaciones').val());
            formData.append('estado', $('#estado').val());
            let imagen = $('#imagen')[0].files[0];

            if (imagen != undefined) {
                formData.append('imagen', imagen);
            }

            $.ajax({

                url: "{{ route('producto.guardarProducto') }}",

                method: "POST",

                data: formData,

                processData: false,
                contentType: false,

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
                },

                error: function (xhr) {

                    console.log(xhr.responseText);

                }

            });
        }

        function editarProducto(producto) {

            $('#id').val(producto.id);
            $('#codigo_barras').val(producto.codigo_barras);
            $('#codigo_interno').val(producto.codigo_interno);
            $('#numero_parte_vehiculo').val(producto.numero_parte_vehiculo);
            $('#nombre').val(producto.nombre);
            $('#descripcion').val(producto.descripcion);
            $('#categoria_id').val(producto.categoria_id);
            $('#sucursal_id').val(producto.sucursal_id);
            $('#proveedor_id').val(producto.proveedor_id);
            $('#marca').val(producto.marca);
            $('#unidad').val(producto.unidad);
            $('#stock_actual').val(producto.stock_actual);
            $('#stock_minimo').val(producto.stock_minimo);
            $('#precio_compra').val(producto.precio_compra);
            $('#precio_venta').val(producto.precio_venta);
            $('#precio_mayor').val(producto.precio_mayor);
            $('#observaciones').val(producto.observaciones);

            if (producto.imagen != null) {

                $('#preview_imagen').attr(
                    'src',
                    '/imagenes/productos/' + producto.imagen
                );

            } else {

                $('#preview_imagen').attr(
                    'src',
                    '/imagenes/productos/default.jpg'
                );

            }
            $('#estado').val(producto.estado);

            $('#modalProducto').modal('show');
        }


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