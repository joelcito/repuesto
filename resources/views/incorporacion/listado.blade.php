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
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" class="form-control form-control-sm" name="nombre" id="nombre">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Descripción</label>
                            <input class="form-control form-control-sm" name="descripcion" id="descripcion">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                Vehículos Compatibles
                            </label>
                            <input class="form-control form-control-sm" name="vehiculos_compatibles"
                                id="vehiculos_compatibles">
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
                            <select class="form-select form-select-sm" name="marca_id" id="marca_id">

                                <option value="">
                                    Seleccione
                                </option>

                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">
                                        {{ $marca->nombre }}
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
                            <select class="form-select form-select-sm" name="unidad_id" id="unidad_id">

                                <option value="">
                                    Seleccione
                                </option>

                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">
                                        {{ $unidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="col-md-4">
                            <label class="form-label fw-bold">Stock Actual</label>
                            <input type="number" class="form-control form-control-sm" name="stock_actual"
                                id="stock_actual">
                        </div> -->
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

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success w-100" onclick="guardarProducto()">Guardar Producto</button>
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


        function convertirAProducto(id) {

            console.log("click ok", id);

            $.ajax({
                url: "{{ route('incorporacion.obtener') }}",
                method: "GET",
                data: { id: id },
                success: function (res) {

                    if (!res.estado) {
                        Swal.fire('Error', res.mensaje, 'error');
                        return;
                    }

                    let inc = res.data;

                    let modal = document.getElementById('modalProducto');

                    if (!modal) {
                        console.error("NO EXISTE modalProducto en esta vista");
                        return;
                    }

                    $('#modalProducto').modal('show');

                    $('#formularioProducto')[0]?.reset();

                    $('#id').val(0);
                    $('#nombre').val(inc.nombre_producto);
                    $('#descripcion').val(inc.descripcion_producto);
                }
            });
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
            formData.append('vehiculos_compatibles', $('#vehiculos_compatibles').val());
            formData.append('categoria_id', $('#categoria_id').val());
            formData.append('marca_id', $('#marca_id').val());
            formData.append('numero_parte_vehiculo', $('#numero_parte_vehiculo').val());
            // formData.append('stock_actual', $('#stock_actual').val());
            formData.append('stock_minimo', $('#stock_minimo').val());
            formData.append('unidad_id', $('#unidad_id').val());
            formData.append('precio_compra', $('#precio_compra').val());
            formData.append('precio_venta', $('#precio_venta').val());
            formData.append('precio_mayor', $('#precio_mayor').val());
            //formData.append('compra_ingreso', $('#compra_ingreso').val());
            formData.append('sucursal_id', $('#sucursal_id').val());
            formData.append('proveedor_id', $('#proveedor_id').val());
            formData.append('observaciones', $('#observaciones').val());

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
    </script>
@endsection