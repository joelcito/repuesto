@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card">
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h1
                                class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                                Formulario de Venta</h1>
                        </div>
                    </div>
                    <hr>
                    <form id="formulario_venta_general">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img id="imagen_cliente" src="{{ asset('assets/img/default.jpg') }}"
                                            alt="Imagen del cliente" width="100%"
                                            style="object-fit:cover; border-radius:10px; border:1px solid #ccc;">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="fs-6 fw-semibold form-label mb-2 required">Seleccionar
                                                    Cliente</label>
                                                <select name="cliente_seleccionado" id="cliente_seleccionado"
                                                    class="form-select form-select-sm" required>
                                                    <option value="">SELECCIONE EL CLIENTE</option>
                                                    @foreach($clientes as $cliente)
                                                        <option value="{{ $cliente->id }}">
                                                            {{ $cliente->nombres }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="cliente_seleccionado_id"
                                                    id="cliente_seleccionado_id">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="fs-6 fw-semibold form-label mb-2">Entregado Por</label>
                                                <input type="text" class="form-control fw-bold form-control-solid"
                                                    name="entregado_por" id="entregado_por">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="fw-semibold fs-6 mb-2">
                                                    Caja
                                                </label>
                                                <select class="form-select form-select-sm" id="caja_id">
                                                    @foreach($cajas as $caja)
                                                        <option value="{{ $caja->id }}">
                                                            CAJA #{{ $caja->id }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="fs-6 fw-semibold form-label mb-2 required">Fecha de
                                            Recepcion</label>
                                        <input type="date" class="form-control fw-bold form-control-solid"
                                            name="fecha_recepcion_cliente" id="fecha_recepcion_cliente" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="fs-6 fw-semibold form-label mb-2">Recibido por</label>
                                        <input type="text" class="form-control fw-bold form-control-solid"
                                            name="recibido_por" id="recibido_por"
                                            value="{{ $usuario->nombres . ' ' . $usuario->ap_paterno . ' ' . $usuario->ap_materno }}"
                                            readonly>
                                        <input type="hidden" name="usuario_recepciono_id" id="usuario_recepciono_id"
                                            value="{{ $usuario->id }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div id="tabla_ventas">
                        <form id="formulario_venta">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="fw-semibold fs-6 mb-2">
                                        Producto
                                    </label>
                                    <select class="form-select form-select-sm" id="producto_id">
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id }}" data-normal="{{ $producto->precio_venta }}"
                                                data-mayor="{{ $producto->precio_mayor }}">
                                                {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">
                                        Cantidad
                                    </label>
                                    <input type="number" id="cantidad" class="form-control form-control-sm" min="1"
                                        value="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">
                                        Tipo Precio
                                    </label>
                                    <select class="form-select form-select-sm" id="tipo_precio">
                                        <option value="NORMAL">
                                            NORMAL
                                        </option>
                                        <option value="MAYOR">
                                            MAYOR
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">
                                        Precio Venta
                                    </label>
                                    <input type="number" step="0.01" id="precio_venta"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md">
                                    <label class="fw-semibold fs-6 mb-2">Obs</label>
                                    <input type="text" id="observacion" name="observacion" style="width: 100%">
                                </div>

                                <div class="col-md-1 d-flex align-items-end">
                                    <div class="d-flex justify-content-center gap-2 w-100">
                                        <button class="btn btn-success btn-circle btn-sm btn-icon" type="button"
                                            onclick="agregarProducto()" title="Agregar producto"
                                            id="boton-agrega-producto">
                                            <i class="fa fa-xs fa-shopping-cart"></i> +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div id="tabla_detalles" style="display: none;">
                            <h3 class="text-center">CARGAR DE PRDUCTOS</h3>
                            <div class="card shadow-sm mb-5">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        DETALLE DE VENTA
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tabla_detalle" class="table align-middle table-row-dashed fs-6 gy-5">
                                            <thead>
                                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Tipo Precio</th>
                                                    <th>Precio</th>
                                                    <th>Subtotal</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalle_venta">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end mt-5">
                                        <div class="bg-light-primary px-5 py-3 rounded">
                                            <h2 class="fw-bold mb-0">
                                                TOTAL:
                                                Bs.
                                                <span id="total_general">
                                                    0.00
                                                </span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="bloque_recibo">
                                <div class="col-md-12 bg-light-success">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="text-center text-success">DATOS DE PAGO</h2>
                                        </div>
                                    </div>
                                    <form id="formularioGeneraRecibo">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="required">Tipo Pago</label>
                                                <select name="tipo_pago_pagado_recibo" id="tipo_pago_pagado_recibo"
                                                    class="form-control form-control-sm"
                                                    onchange="validarCamposRecibo()">
                                                    <option value="">Seleccione</option>
                                                    <option value="EFECTIVO">EFECTIVO</option>
                                                    <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                                    <option value="QR">QR</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="required">Realizara algun Pago?</label>
                                                <div class="d-flex align-items-center mt-3">
                                                    <label class="form-check form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input h-20px w-20px" type="checkbox"
                                                            name="realizo_pago_recibo" value="pago"
                                                            id="realizo_pago_recibo" />
                                                        <span class="form-check-label fw-semibold">Realizo un
                                                            pago</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="required">Monto Venta</label>
                                                <input type="number" class="form-control form-control-sm" readonly
                                                    id="monto_total_pagado_recibo" name="monto_total_pagado_recibo"
                                                    value="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="required">Monto Pagado</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    id="monto_pagado_recibo" name="monto_pagado_recibo" value="0"
                                                    onkeyup="caluclarCambioRecibo(this)">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="required">Cambio</label>
                                                <input type="number" class="form-control form-control-sm" readonly
                                                    id="cambio_pagado_recibo" name="cambio_pagado_recibo" value="0">
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <button class="btn btn-sm w-100 btn-success" type="button"
                                                onclick="guardarVenta()" id="boton_enviar_recibo"> <i
                                                    class="fa fa-spinner fa-spin"
                                                    style="display:none;"></i>GUARDAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        let productosVenta = [];
        $(document).ready(function () {
            tabla = $('#tabla_detalle').DataTable({
                responsive: true,
                searching: false,
                paging: false,
                info: false
            });
            actualizarPrecio();
        });
        $('#producto_id').change(function () {
            actualizarPrecio();
        });
        $('#tipo_precio').change(function () {
            actualizarPrecio();
        });
        $('#realizo_pago_recibo').change(function () {
            validarCamposRecibo();
        });
        function actualizarPrecio() {
            let producto =
                $('#producto_id option:selected');
            let tipoPrecio =
                $('#tipo_precio').val();
            let precio = 0;
            if (tipoPrecio == 'MAYOR') {
                precio = producto.data('mayor');
            } else {
                precio = producto.data('normal');
            }
            $('#precio_venta').val(
                parseFloat(precio).toFixed(2)
            );
        }

        function agregarProducto() {

            $('#tabla_detalles').show();

            let producto = $('#producto_id option:selected');

            let producto_id = producto.val();

            let nombre = producto.text();

            let cantidad = parseFloat($('#cantidad').val());

            let tipo_precio = $('#tipo_precio').val();

            let precio = parseFloat($('#precio_venta').val());

            let subtotal = cantidad * precio;

            if (cantidad <= 0) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Cantidad inválida'
                });

                return;
            }

            let existe = productosVenta.find(
                item =>
                    item.producto_id == producto_id
                    &&
                    item.tipo_precio == tipo_precio
            );

            if (existe) {

                existe.cantidad =
                    parseFloat(existe.cantidad)
                    +
                    cantidad;

                existe.subtotal =
                    existe.cantidad * existe.precio;

                recargarTabla();

                calcularTotal();

                return;
            }

            productosVenta.push({
                producto_id: producto_id,
                nombre: nombre,
                cantidad: cantidad,
                tipo_precio: tipo_precio,
                precio: precio,
                subtotal: subtotal
            });

            recargarTabla();

            calcularTotal();
        }

        function recargarTabla() {

            tabla.clear().draw();

            productosVenta.forEach(function (item) {

                let botonEliminar =
                    `<button
                        class="btn btn-danger btn-sm"
                        onclick="eliminarProducto(${item.producto_id}, '${item.tipo_precio}')">
                        X
                    </button>`;

                tabla.row.add([
                    item.nombre,
                    item.cantidad,
                    item.tipo_precio,
                    parseFloat(item.precio).toFixed(2),
                    parseFloat(item.subtotal).toFixed(2),
                    botonEliminar
                ]).draw();
            });
        }


        function calcularTotal() {
            let total = 0;
            productosVenta.forEach(function (item) {
                total += item.cantidad * item.precio;
            });
            $('#total_general').text(
                total.toFixed(2)
            );
            $('#monto_total_pagado_recibo').val(
                total.toFixed(2)
            );
        }


        function validarCamposRecibo() {
            let realizoPago =
                $('#realizo_pago_recibo').is(':checked');
            if (realizoPago) {
                $('#monto_pagado_recibo')
                    .prop('readonly', false);
                $('#tipo_pago_pagado_recibo')
                    .prop('disabled', false);
            } else {
                $('#monto_pagado_recibo')
                    .prop('readonly', true)
                    .val(0);
                $('#cambio_pagado_recibo')
                    .val(0);
                $('#tipo_pago_pagado_recibo')
                    .val('');
            }
        }

        function eliminarProducto(producto_id, tipo_precio) {

            productosVenta =
                productosVenta.filter(
                    item =>
                        !(
                            item.producto_id == producto_id
                            &&
                            item.tipo_precio == tipo_precio
                        )
                );

            recargarTabla();

            calcularTotal();

            if (productosVenta.length == 0) {

                $('#tabla_detalles').hide();
            }
        }

        function caluclarCambioRecibo(select) {
            let total = parseFloat(
                $('#total_general').text()
            ) || 0;
            let pagado = parseFloat(
                select.value
            ) || 0;
            let cambio = 0;
            if (pagado > total) {
                cambio = pagado - total;
            }
            $('#cambio_pagado_recibo').val(
                cambio.toFixed(2)
            );
        }

        function guardarVenta() {

            if (
                !$('#formulario_venta_general')[0]
                    .checkValidity()
            ) {

                $('#formulario_venta_general')[0]
                    .reportValidity();

                return;
            }
            let realizoPago =
                $('#realizo_pago_recibo')
                    .is(':checked');

            let montoPagado = 0;

            if (realizoPago) {

                montoPagado =
                    parseFloat(
                        $('#monto_pagado_recibo').val()
                    ) || 0;
            }

            let boton =
                $("#boton_enviar_recibo");

            let icono =
                boton.find("i");

            boton.attr("disabled", true);

            icono.show();
            let datos = {
                cliente_id:
                    $('#cliente_seleccionado').val(),
                caja_id:
                    $('#caja_id').val(),
                fecha:
                    $('#fecha_recepcion_cliente').val(),
                metodo_pago:
                    $('#tipo_pago_pagado_recibo').val(),
                monto_pagado:
                    $('#monto_pagado_recibo').val(),
                cambio:
                    $('#cambio_pagado_recibo').val(),
                observacion:
                    $('#observacion').val(),
                realizo_pago:
                    realizoPago,
                productos:
                    productosVenta
            };

            $.ajax({
                url: "{{ route('venta.guardarVenta') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {

                    boton.attr("disabled", false);

                    icono.hide();
                    if (resultado.estado) {
                        Swal.fire({
                            icon: 'success',
                            title: resultado.mensaje,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // window.location =
                        //     "{{ route('venta.listado') }}";
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: resultado.mensaje
                        });
                    }
                },
                error: function (xhr) {
                    boton.attr("disabled", false);

                    icono.hide();

                    console.log(xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al guardar'
                    });

                    console.log(xhr.responseText);
                }
            });
        }
    </script>
@endsection