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
            <div class="modal-body">

                <form id="formularioProducto">
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Código Barras</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" name="codigo_barras"
                                            id="codigo_barras">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="generarCodigoBarras()">
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
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Stock Mínimo</label>
                                    <input type="number" class="form-control form-control-sm" name="stock_minimo"
                                        id="stock_minimo">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Sucursal</label>
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
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                        name="precio_compra" id="precio_compra" @if(auth()->user()->esOperador())
                                        readonly @endif>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Precio Venta</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                        name="precio_venta" id="precio_venta" @if(auth()->user()->esOperador()) readonly
                                        @endif>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Precio Mayor</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                        name="precio_mayor" id="precio_mayor" @if(auth()->user()->esOperador()) readonly
                                        @endif>
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
                                    <label class="form-label fw-bold">Medida/Especificación</label>
                                    <input type="text" class="form-control form-control-sm" id="medidas" name="medidas">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Ubicación</label>
                                    <input type="text" class="form-control form-control-sm" id="ubicacion"
                                        name="ubicacion">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo Producto</label>
                                    <select class="form-select form-select-sm" name="tipo_producto" id="tipo_producto">
                                        <option value="REPUESTO">REPUESTO</option>
                                        <option value="LUBRICANTE">LUBRICANTE</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="row">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <h5 class="mb-0">Imágenes</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="border rounded p-2 mb-3 text-center">
                                            <img id="imagenPrincipal" src="/imagenes/productos/default.jpg"
                                                class="img-fluid rounded"
                                                style="max-height:320px; object-fit:contain; cursor:pointer;">
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <button type="button" class="btn btn-light" onclick="imagenAnterior()">
                                                <i class="fa fa-chevron-left"></i>
                                            </button>

                                            <button type="button" class="btn btn-light" onclick="imagenSiguiente()">
                                                <i class="fa fa-chevron-right"></i>
                                            </button>
                                        </div>
                                        <div id="preview_imagenes"
                                            class="d-flex flex-wrap gap-2 justify-content-center">
                                        </div>
                                        <hr>
                                        <label class="form-label fw-bold"> Agregar imágenes </label>
                                        <input type="file" id="imagenes" name="imagenes[]" multiple
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
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

<div class="modal fade" id="modalStockSucursal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">STOCK POR SUCURSALES <span class="text-info" id="nombreProductoModal"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <div id="tabla_stock"></div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalIngreso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">INGRESO DE STOCK: <span class="text-info" id="nombreProductoModal-1"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioIngreso">
                    <input type="hidden" id="idProd" name="idProd">
                    <input type="hidden" id="idSuc" name="idSuc">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sucursal</label>
                            <input type="text" class="form-control form-control-sm" id="sucursal" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha</label>
                            <input type="text" class="form-control form-control-sm" value="{{ date('Y-m-d') }}"
                                readonly>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                Cantidad
                            </label>
                            <input type="number" class="form-control form-control-sm" id="cantidad_ingreso"
                                name="cantidad" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                Precio Compra
                            </label>
                            <input type="number" class="form-control form-control-sm" id="precio_compra_ingreso"
                                name="precio_compra" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                Precio Venta
                            </label>
                            <input type="number" class="form-control form-control-sm" id="precio_venta_ingreso"
                                name="precio_venta" min="0.01" step="0.01">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                Descripción
                            </label>

                            <textarea class="form-control form-control-sm" id="descripcion_ingreso"
                                name="descripcion"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="guardarIngreso()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSalida" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">SALIDA DE STOCK: <span class="text-info" id="nombreProductoModal-2"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioSalida">
                    <input type="hidden" id="idProds" name="idProds">
                    <input type="hidden" id="idSucs" name="idSucs">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Sucursal
                            </label>
                            <input type="text" class="form-control form-control-sm" id="sucursales" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Fecha
                            </label>
                            <input type="text" class="form-control form-control-sm" value="{{ date('Y-m-d') }}"
                                readonly>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Cantidad
                            </label>

                            <input type="number" class="form-control form-control-sm" id="cantidad_salida"
                                name="cantidad" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Motivo
                            </label>
                            <select class="form-select form-select-sm" id="motivo" name="motivo" required>
                                <option value="">Seleccione</option>
                                <option value="PERDIDA">
                                    Pérdida
                                </option>
                                <option value="ROBO">
                                    Robo
                                </option>
                                <option value="DETERIORO">
                                    Deterioro
                                </option>
                                <option value="VENTA">
                                    Venta
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                Descripción
                            </label>
                            <textarea class="form-control form-control-sm" id="descripcion_salida"
                                name="descripcion"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="guardarSalida()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalTransferencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">
                    TRANSFERENCIA DE PRODUCTO
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="formularioTransferencia">
                    @csrf
                    <input type="hidden" id="producto_transferencia_id" name="producto_id">
                    <input type="hidden" id="sucursal_origen_id" name="sucursal_origen_id">
                    <div class="mb-4">
                        <label class="form-label">
                            Sucursal Origen
                        </label>
                        <input type="text" id="sucursal_origen_nombre" class="form-control" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">
                            Sucursal Destino
                        </label>
                        <select class="form-select" name="sucursal_destino_id" id="sucursal_destino_id" required>
                            <option value="">
                                Seleccione una sucursal
                            </option>
                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">
                            Cantidad
                        </label>

                        <input type="number" class="form-control" name="cantidad" id="cantidad_transferencia" min="1"
                            required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">
                            Descripción
                        </label>
                        <textarea class="form-control" name="descripcion" id="descripcion_transferencia"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="guardarTransferencia()">
                    Transferir
                </button>
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

                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="buscar" class="form-control form-control-sm"
                            placeholder="Buscar producto...">
                    </div>

                    <div class="col-md-2">
                        <select id="f_categoria" class="form-select form-select-sm">
                            <option value="">Categoría</option>
                            @foreach($categorias as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select id="f_marca" class="form-select form-select-sm">
                            <option value="">Marca</option>
                            @foreach($marcas as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select id="f_estado" class="form-select form-select-sm">
                            <option value="">Estado</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select id="f_stock" class="form-select form-select-sm">
                            <option value="">Stock</option>
                            <option value="con">Con stock</option>
                            <option value="sin">Sin stock</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-sm btn-secondary w-100" onclick="limpiarFiltros()">X Limpiar</button>
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
        });

        $(document).ready(function () {
            ajaxListado();
            $('#modalProducto').on('hidden.bs.modal', function () {
                limpiarModalProducto();
            });

            $('input, select, textarea').on('input change', function () {
                if ($(this).val() && $(this).val().trim() !== '') {
                    $(this).removeClass('is-invalid');
                }
            });

        });



        function ajaxListado() {
            $.ajax({
                url: "{{ route('producto.ajaxListado') }}",
                method: "POST",
                data: {
                    buscar: $('#buscar').val(),
                    estado: $('#f_estado').val(),
                    stock: $('#f_stock').val(),
                    categoria: $('#f_categoria').val(),
                    marca: $('#f_marca').val(),
                },
                success: function (res) {
                    $('#table_listado').html(res.data.listado);
                }
            });
        }
        function modalNuevoProducto() {
            $('#formularioProducto')[0].reset();
            $('#id').val(0);
            listaImagenes = [];
            indiceActual = 0;
            $('#preview_imagenes').html('');
            $('#imagenPrincipal').attr(
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
            $.ajax({
                url: "/producto/generar-codigo",
                method: "POST",
                success: function (codigo) {
                    $('#codigo_barras').val(codigo);
                }
            });
        }

        function guardarProducto() {
            if (!validarProducto()) {
                Swal.fire({
                    icon: "warning",
                    title: "Complete los campos obligatorios"
                });
                return;
            }
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
            formData.append('stock_minimo', $('#stock_minimo').val());
            formData.append('unidad_id', $('#unidad_id').val());
            formData.append('precio_compra', $('#precio_compra').val());
            formData.append('precio_venta', $('#precio_venta').val());
            formData.append('precio_mayor', $('#precio_mayor').val());
            formData.append('sucursal_id', $('#sucursal_id').val());
            formData.append('proveedor_id', $('#proveedor_id').val());
            formData.append('observaciones', $('#observaciones').val());
            formData.append('medidas', $('#medidas').val());
            formData.append('ubicacion', $('#ubicacion').val());
            formData.append('tipo_producto', $('#tipo_producto').val());

            listaImagenes.forEach(img => {
                if (img.file instanceof File) {
                    formData.append('imagenes[]', img.file);
                }
            });

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
            limpiarModalProducto();
            $('#id').val(producto.id);
            $('#codigo_barras').val(producto.codigo_barras);
            $('#codigo_interno').val(producto.codigo_interno);
            $('#numero_parte_vehiculo').val(producto.numero_parte_vehiculo);
            $('#nombre').val(producto.nombre);
            $('#descripcion').val(producto.descripcion);
            $('#vehiculos_compatibles').val(producto.vehiculos_compatibles);
            $('#categoria_id').val(producto.categoria_id);
            $('#sucursal_id').val(producto.sucursal_id);
            $('#proveedor_id').val(producto.proveedor_id);
            $('#marca_id').val(producto.marca_id);
            $('#unidad_id').val(producto.unidad_id);
            $('#stock_minimo').val(producto.stock_minimo);
            $('#precio_compra').val(producto.precio_compra);
            $('#precio_venta').val(producto.precio_venta);
            $('#precio_mayor').val(producto.precio_mayor);
            $('#observaciones').val(producto.observaciones);
            $('#medidas').val(producto.medidas);
            $('#ubicacion').val(producto.ubicacion);
            $('#tipo_producto').val(producto.tipo_producto);
            indiceActual = 0;
            listaImagenes = [];
            if (producto.imagenes && producto.imagenes.length > 0) {
                producto.imagenes.forEach(img => {
                    listaImagenes.push({
                        id: img.id,
                        file: null,
                        existente: true,
                        url: '/imagenes/productos/' + img.imagen
                    });
                });
            }
            renderPreview();
            $('#modalProducto').modal('show');
        }

        function abrirStock(productoId, nombre) {
            document.getElementById('nombreProductoModal').textContent = nombre;
            document.getElementById('nombreProductoModal-1').textContent = nombre;
            document.getElementById('nombreProductoModal-2').textContent = nombre;
            $.ajax({
                url: "{{ route('movimiento.ajaxListado') }}",
                type: 'GET',
                data: {
                    productoId: productoId,
                    nombre: nombre
                },
                success: function (res) {
                    if (res.estado) {
                        $('#tabla_stock').html(res.data.stock);
                        $('#modalStockSucursal').modal('show');
                    } else {
                        Swal.fire('Error', 'No se pudo obtener el stock', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Ocurrió un error al obtener el stock', 'error');
                }
            });
        }


        function modalIngreso(productoId, sucursalId, nombreSuc) {

            $.ajax({
                url: '/producto/' + productoId,
                method: 'GET',
                success: function (prod) {
                    $('#modalIngreso').modal('show');
                    setTimeout(() => {
                        $('#sucursal').val(nombreSuc);
                        $('#idSuc').val(sucursalId);
                        $('#idProd').val(productoId);
                        $('#precio_compra_ingreso').val(prod.precio_compra || 0);
                        $('#precio_venta_ingreso').val(prod.precio_venta || 0);
                        $('#cantidad_ingreso').val('');
                        $('#descripcion_ingreso').val('');
                    }, 200);

                }
            });
        }

        function guardarIngreso() {
            if ($("#formularioIngreso")[0].checkValidity()) {
                let datos = $('#formularioIngreso').serializeArray();
                $.ajax({
                    url: "{{ route('movimiento.guardarIngreso') }}",
                    method: "POST",
                    data: datos,
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "INGRESO REGISTRADO",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            ajaxListado();
                            $('#modalIngreso').modal('hide');
                            $('#modalStockSucursal').modal('hide');

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resultado.mensaje
                            });
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
                                input.after(`
                                                                    <div class="invalid-feedback">
                                                                        ${mensaje}
                                                                    </div>
                                                                `);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.'
                            });
                        }
                    }
                });

            } else {
                $("#formularioIngreso")[0].reportValidity();
            }
        }

        function modalSalida(productoId, sucursalId, nombreSuc) {
            let datos = $('#formularioIngreso').serializeArray();
            $.ajax({
                url: "{{ route('movimiento.sacarTipoIngreso') }}",
                method: "POST",
                data: {
                    producto: productoId,
                    sucursal: sucursalId
                },
                success: function (resultado) {
                    if (resultado.estado) {
                        let datos = resultado.data.select
                        let $select = $('#movimiento_id_ingreso');
                        $select.empty();
                        $select.append('<option value="">Seleccione una opción</option>');
                        $.each(datos, function (index, element) {
                            $select.append(
                                $('<option>', {
                                    value: element.id,
                                    text: 'Compra: ' + element.compra_ingreso + ' | Cantidad: ' + element.cantidad
                                })
                            );
                        });

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
            });
        }

        function guardarSalida() {
            if ($("#formularioSalida")[0].checkValidity()) {
                let datos = $('#formularioSalida').serializeArray();
                $.ajax({
                    url: "{{ route('movimiento.guardarSalida') }}",
                    method: "POST",
                    data: datos,
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "SALIDA REGISTRADA",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            ajaxListado();
                            $('#modalSalida').modal('hide');
                            $('#modalStockSucursal').modal('hide');

                        } else {
                            Swal.fire({
                                title: resultado.mensaje,
                                icon: "error"
                            });
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
                                input.after(`
                                                                    <div class="invalid-feedback">
                                                                        ${mensaje}
                                                                    </div>
                                                                `);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.'
                            });
                        }
                    }
                });
            } else {

                $("#formularioSalida")[0].reportValidity();
            }
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

        function modalTransferencia(
            productoId,
            sucursalId,
            nombreSucursal
        ) {

            $('#producto_transferencia_id').val(productoId);
            $('#sucursal_origen_id').val(sucursalId);
            $('#sucursal_origen_nombre').val(nombreSucursal);
            $('#cantidad_transferencia').val('');
            $('#descripcion_transferencia').val('');
            $('#sucursal_destino_id').val('');
            $('#modalTransferencia').modal('show');
        }
        function guardarTransferencia() {
            if ($("#formularioTransferencia")[0].checkValidity()) {
                let datos =
                    $('#formularioTransferencia').serializeArray();
                $.ajax({
                    url: "{{ route('movimiento.guardarTransferencia') }}",
                    method: "POST",
                    data: datos,
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "TRANSFERENCIA REALIZADA",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#modalTransferencia').modal('hide');
                            ajaxListado();
                        } else {
                            Swal.fire({
                                title: resultado.mensaje,
                                icon: "error"
                            });
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
            } else {

                $("#formularioTransferencia")[0]
                    .reportValidity();
            }
        }

        let listaImagenes = [];
        let indiceActual = 0;

        $('#imagenes').on('change', function (e) {
            let archivos = Array.from(e.target.files);
            archivos.forEach(file => {
                let id = Math.random().toString(36).substr(2, 9);
                listaImagenes.push({
                    id: id,
                    file: file,
                    url: URL.createObjectURL(file)
                });
            });
            renderPreview();
            if (listaImagenes.length > 0) {
                indiceActual = listaImagenes.length - archivos.length;
                $('#imagenPrincipal').attr('src', listaImagenes[indiceActual].url);
            }
            $('#imagenes').val('');
        });

        function renderPreview() {
            $('#preview_imagenes').html('');
            listaImagenes.forEach((img, index) => {
                $('#preview_imagenes').append(`
                                            <div class="position-relative d-inline-block">

                                                <img
                                                    src="${img.url}"
                                                    width="80"
                                                    height="80"
                                                    class="img-thumbnail ${index == indiceActual ? 'border border-primary border-3' : ''}"
                                                    style="cursor:pointer;object-fit:cover"
                                                    onclick="mostrarImagen(${index})">

                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm position-absolute"
                                                    style="top:-8px;right:-8px;border-radius:50%;width:24px;height:24px;padding:0;"
                                                    onclick="eliminarImagen('${img.id}')">

                                                    ×   
                                                </button>

                                            </div>
                                        `);
            });

            if (listaImagenes.length > 0) {
                if (indiceActual >= listaImagenes.length) {
                    indiceActual = 0;
                }
                $('#imagenPrincipal').attr('src', listaImagenes[indiceActual].url);
            } else {
                $('#imagenPrincipal').attr('src', '/imagenes/productos/default.jpg');
            }
        }
        function eliminarImagen(id) {
            let eliminado = listaImagenes.findIndex(x => x.id == id);
            if (eliminado == -1)
                return;
            listaImagenes.splice(eliminado, 1);
            if (indiceActual >= listaImagenes.length)
                indiceActual = listaImagenes.length - 1;
            if (indiceActual < 0)
                indiceActual = 0;
            if (listaImagenes.length > 0) {
                $('#imagenPrincipal').attr(
                    'src',
                    listaImagenes[indiceActual].url
                );
            } else {
                $('#imagenPrincipal').attr(
                    'src',
                    '/imagenes/productos/default.jpg'
                );
            }
            renderPreview();
        }


        function imagenSiguiente() {
            if (listaImagenes.length == 0)
                return;
            indiceActual++;
            if (indiceActual >= listaImagenes.length)
                indiceActual = 0;
            mostrarImagen(indiceActual);
        }

        function imagenAnterior() {
            if (listaImagenes.length == 0)
                return;
            indiceActual--;
            if (indiceActual < 0)
                indiceActual = listaImagenes.length - 1;
            mostrarImagen(indiceActual);
        }

        function mostrarImagen(index) {
            indiceActual = index;
            $('#imagenPrincipal').attr(
                'src',
                listaImagenes[index].url
            );
        }

        function limpiarModalProducto() {
            $('#formularioProducto')[0].reset();
            $('#id').val(0);
            listaImagenes = [];
            indiceActual = 0;
            $('#preview_imagenes').html('');
            $('#imagenPrincipal').attr('src', '/imagenes/productos/default.jpg');
            $('#imagenes').val('');
        }

        function validarProducto() {

            let campos = [
                'nombre',
                'categoria_id',
                'marca_id',
                'unidad_id',
                'codigo_barras',
                'proveedor_id',
                'sucursal_id',
                'stock_minimo',
                'precio_compra',
                'precio_venta',
                'precio_mayor',
            ];

            $('.is-invalid').removeClass('is-invalid');

            let ok = true;

            campos.forEach(campo => {

                let el = $('#' + campo);

                if (!el.val() || el.val().trim() === '') {
                    el.addClass('is-invalid');
                    ok = false;
                }
            });
            return ok;
        }


        function cargarProductos() {
            $.ajax({
                url: "{{ route('producto.ajaxListado') }}",
                method: "POST",
                data: {
                    buscar: $('#buscar').val(),
                    categoria: $('#f_categoria').val(),
                    marca: $('#f_marca').val(),
                    estado: $('#f_estado').val(),
                    stock: $('#f_stock').val(),
                },
                success: function (res) {
                    if (res.estado) {
                        $('#table_listado').html(res.data.listado);
                    }
                }
            });
        }

        $(document).on('input', '#buscar', function () {
            ajaxListado();
        });

        $(document).on('change', '#f_estado, #f_stock , #f_categoria, #f_marca', function () {
            ajaxListado();
        });

        function limpiarFiltros() {
            $('#buscar').val('');
            $('#f_estado').val('');
            $('#f_stock').val('');
            ajaxListado();
        }
    </script>
@endsection