@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

@if($cajas->count() == 0)
    <div class="alert alert-danger">
        <h4 class="mb-1">
            No existe una caja aperturada
        </h4>
        <p class="mb-0">
            Debe aperturar una caja para realizar ventas.
        </p>
    </div>
@endif

@if($cajas->count() > 0)
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
                                                            <option value="{{ $cliente->id }}"
                                                                data-imagen="{{ $cliente->imagen }}">
                                                                {{ $cliente->nombres }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="cliente_seleccionado_id"
                                                        id="cliente_seleccionado_id">
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
                                                name="fecha_recepcion_cliente" id="fecha_recepcion_cliente" required
                                                value="{{date('Y-m-d')}}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="fs-6 fw-semibold form-label mb-2">Vendido por</label>
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
                                    <div class="col-md-6 position-relative">
                                        <label class="fw-semibold fs-6 mb-2">
                                            Buscar Producto
                                        </label>
                                        <input type="text" id="buscar_producto" class="form-control form-control-lg"
                                            placeholder="Ingrese Nombre, Marca, Código, Nro Parte,Vehículo...">
                                        <div id="resultado_productos" class="shadow bg-white border rounded mt-1" style="
                                                                        max-height:400px;
                                                                        overflow-y:auto;
                                                                        display:none;
                                                                        position:absolute;
                                                                        z-index:9999;
                                                                        width:100%;
                                                                    ">
                                        </div>

                                        <input type="hidden" id="producto_id" name="producto_id">

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
                                            class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="fw-semibold fs-6 mb-2">
                                            Subtotal
                                        </label>

                                        <input type="number" id="subtotal_preview" class="form-control form-control-sm"
                                            readonly value="0.00">
                                    </div>
                                    <div class="col-md">
                                        <label class="fw-semibold fs-6 mb-2">Obs</label>
                                        <input type="text" id="observacion" name="observacion"
                                            class="form-control form-control-sm">
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
                                <h3 class="text-center">CARGAR PRODUCTOS</h3>
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
                                                        <th>Imagen</th>
                                                        <th>Producto</th>
                                                        <th>Código Interno</th>
                                                        <th>Marca</th>
                                                        <th>Vehiculo</th>
                                                        <th>Cantidad</th>
                                                        <th>Ubicación</th>
                                                        <th>Medida/Especificación</th>
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
                                                <div class="col-md-12">
                                                    <label class="fw-bold text-success">Pagos realizados</label>

                                                    <div id="lista_pagos">

                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-12 text-end">
                                                            <h4 class="fw-bold">
                                                                Cambio:
                                                                <span id="cambio_visual" class="text-success">
                                                                    0.00
                                                                </span>
                                                            </h4>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" id="cambio_pagado_recibo" value="0">

                                                    <button type="button" class="btn btn-sm btn-primary mt-2"
                                                        onclick="agregarPago()">
                                                        + Agregar pago
                                                    </button>
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
@endif

@stop()
@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        let productoSeleccionado = null;
        let productosVenta = [];
        let tabla = null;
        $(document).ready(function () {
            tabla = $('#tabla_detalle').DataTable({
                responsive: true,
                searching: false,
                paging: false,
                info: false
            });
            actualizarPrecio();
            validarCamposRecibo();
            agregarPago();
            $(document).on('input', '.monto_pago, .descuento_pago', function () {
                calcularCambioGlobal();
            });

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
        $('#cantidad, #precio_venta').on('keyup change', function () {
            calcularSubtotalPreview();

        });

        let precioNormalSeleccionado = 0;
        let precioMayorSeleccionado = 0;

        function calcularSubtotalPreview() {
            let cantidad =
                parseFloat($('#cantidad').val()) || 0;
            let precio =
                parseFloat($('#precio_venta').val()) || 0;
            let subtotal = cantidad * precio;
            $('#subtotal_preview').val(
                subtotal.toFixed(2)
            );
        }
        // function actualizarPrecio() {

        //     let tipoPrecio = $('#tipo_precio').val();

        //     let precio = 0;

        //     if (tipoPrecio == 'MAYOR') {
        //         precio = precioMayorSeleccionado;
        //     } else {
        //         precio = precioNormalSeleccionado;

        //         $('#precio_venta').val(
        //             parseFloat(precio).toFixed(2)
        //         );

        //         calcularSubtotalPreview();
        //     }
        // }


        function actualizarPrecio() {

            let tipoPrecio = $('#tipo_precio').val();

            let precio = 0;

            if (tipoPrecio == 'MAYOR') {
                precio = precioMayorSeleccionado;
            } else {
                precio = precioNormalSeleccionado;
            }

            $('#precio_venta').val(parseFloat(precio).toFixed(2));
            calcularSubtotalPreview();
        }


        function agregarProducto() {

            $('#tabla_detalles').show();
            let producto_id = $('#producto_id').val();
            let nombre = $('#buscar_producto').val();

            let cantidad = parseFloat($('#cantidad').val());
            let tipo_precio = $('#tipo_precio').val();
            let precio = parseFloat($('#precio_venta').val());
            let subtotal = cantidad * precio;
            if (producto_id == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Seleccione un producto'
                });
                return;
            }

            if (cantidad <= 0 || isNaN(cantidad)) {
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
                existe.cantidad = parseFloat(existe.cantidad) + cantidad;
                existe.subtotal = existe.cantidad * existe.precio;
                recargarTabla();
                calcularTotal();
                return;
            }
            console.log(productoSeleccionado);
            productosVenta.push({
                producto_id: producto_id,
                nombre: productoSeleccionado.nombre,
                codigo_interno: productoSeleccionado.codigo_interno || '-',
                marca: productoSeleccionado.marca || 'SIN MARCA',
                vehiculo: productoSeleccionado.vehiculo || '-',
                imagen: productoSeleccionado.imagen || null,
                cantidad: cantidad,
                tipo_precio: tipo_precio,
                precio: precio,
                descuento: 0,
                ubicacion: productoSeleccionado.ubicacion || '-',
                medida: productoSeleccionado.medida || '-',

                subtotal: subtotal
            });

            recargarTabla();
            calcularTotal();

            $('#producto_id').val('');
            $('#buscar_producto').val('');
            $('#cantidad').val(1);
            $('#precio_venta').val('');
            $('#subtotal_preview').val('0.00');
        }

        function recargarTabla() {
            tabla.clear().draw();
            productosVenta.forEach(function (item) {

                let imagen = item.imagen
                    ? `/imagenes/productos/${item.imagen}`
                    : `/imagenes/productos/default.jpg`;

                let img = `
                                                    <img src="${imagen}"
                                                    class="imagen-producto"
                                                            width="45"
                                                            height="45"
                                                            style="object-fit:cover;border-radius:6px;cursor:pointer;">
                                                `;

                let botonEliminar =
                    `<button
                                                    class="btn btn-danger btn-sm"
                                                    onclick="eliminarProducto(${item.producto_id}, '${item.tipo_precio}')">
                                                    X
                                                </button>`;

                tabla.row.add([
                    img,
                    item.nombre,
                    item.codigo_interno,
                    item.marca,
                    item.vehiculo,
                    item.cantidad,
                    item.ubicacion,
                    item.medida,
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
            let realizoPago = $('#realizo_pago_recibo').is(':checked');

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
                    .val('')
                    .prop('disabled', true);
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

        function calcularCambioGlobal() {

            let totalVenta = parseFloat($('#total_general').text()) || 0;
            let descuentoGlobal = parseFloat($('#descuento').val()) || 0;
            let totalFinal = totalVenta - descuentoGlobal;
            let totalPagado = 0;

            $('.pago-item').each(function () {
                let monto = parseFloat($(this).find('.monto_pago').val()) || 0;
                let descuento = parseFloat($(this).find('.descuento_pago').val()) || 0;
                totalPagado += (monto - descuento);
            });

            let cambio = totalPagado - totalFinal;
            if (cambio < 0) {
                cambio = 0;
            }
            $('#cambio_pagado_recibo').val(cambio.toFixed(2));
            $('#cambio_visual').text(cambio.toFixed(2));
        }

        function guardarVenta() {
            let pagos = [];
            if (!$('#formulario_venta_general')[0].checkValidity()
            ) {
                $('#formulario_venta_general')[0].reportValidity();
                return;
            }
            $('.pago-item').each(function () {
                let metodo = $(this).find('.metodo_pago').val();
                let monto = parseFloat($(this).find('.monto_pago').val()) || 0;

                if (monto > 0) {
                    pagos.push({
                        metodo: metodo,
                        monto: monto,
                        descuento: parseFloat($(this).find('.descuento_pago').val()) || 0
                    });
                }
            });
            if (pagos.length == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Debe agregar al menos un pago'
                });
                return;
            }

            let totalVenta = parseFloat($('#total_general').text()) || 0;
            let descuentoGlobal = parseFloat($('#descuento').val()) || 0;
            let totalFinal = totalVenta - descuentoGlobal;
            let totalPagado = 0;
            pagos.forEach(p => {
                let monto = parseFloat(p.monto) || 0;
                let desc = parseFloat(p.descuento) || 0;

                totalPagado += (monto - desc);
            });

            if (totalPagado < totalFinal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pago incompleto',
                    text: 'El pago es menor al total de la venta'
                });
                return;
            }

            let realizoPago =
                $('#realizo_pago_recibo').is(':checked');

            let montoPagado = 0;
            if (realizoPago) {
                montoPagado =
                    parseFloat($('#monto_pagado_recibo').val()) || 0;
            }
            let boton = $("#boton_enviar_recibo");
            let icono = boton.find("i");
            boton.attr("disabled", true);
            icono.show();

            let metodoPago = realizoPago
                ? $('#tipo_pago_pagado_recibo').val()
                : 'SIN_PAGO';

            let datos = {
                cliente_id: $('#cliente_seleccionado').val(),
                caja_id: $('#caja_id').val(),
                fecha: $('#fecha_recepcion_cliente').val(),
                metodo_pago: metodoPago,
                monto_pagado: montoPagado,
                cambio: $('#cambio_pagado_recibo').val(),
                observacion: $('#observacion').val(),
                descuento: parseFloat($('#descuento').val()) || 0,
                realizo_pago: realizoPago,
                productos: productosVenta,
                pagos: pagos
            };

            $.ajax({
                url: "{{ route('venta.guardarVenta') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    boton.attr("disabled", false);
                    icono.hide();
                    if (resultado.estado) {
                        limpiarFormularioVenta();
                        Swal.fire({
                            icon: 'success',
                            title: resultado.mensaje,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        window.open(
                            '/venta/recibo/' + resultado.venta_id,
                            '_blank'
                        );
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

        $('#buscar_producto').keyup(function () {
            let buscar = $(this).val();
            if (buscar.length < 2) {
                $('#resultado_productos').hide();
                return;
            }
            $.ajax({
                url: "{{ route('venta.buscarProductos') }}",
                method: "POST",
                data: {
                    buscar: buscar
                },
                success: function (productos) {
                    let html = '';
                    if (productos.length == 0) {
                        html = `
                                                        <div class="p-3 text-center text-danger">
                                                            No se encontraron productos
                                                        </div>
                                                    `;
                    } else {
                        productos.forEach(producto => {
                            let imagen = producto.imagenes?.length > 0
                                ? `/imagenes/productos/${producto.imagenes[0].imagen}`
                                : `/imagenes/productos/default.jpg`;
                            html += `
                                            <div class="producto-item p-3 border-bottom"
                                                style="
                                                    cursor:pointer;
                                                    transition:0.2s;
                                                "
                                                data-id="${producto.id}"
                                                data-nombre="${producto.nombre}"
                                                data-precio="${producto.precio_venta}"
                                                data-precio-mayor="${producto.precio_mayor}"
                                                data-codigo-interno="${producto.codigo_interno ?? ''}"
                                                data-marca="${producto.marca ? producto.marca.nombre : ''}"
                                                data-vehiculo="${producto.vehiculos_compatibles ?? ''}"
                                                data-ubicacion="${producto.ubicacion ?? ''}"
                                                data-medida="${producto.medida ?? ''}"
                                                data-imagen="${producto.imagenes?.length > 0 ? producto.imagenes[0].imagen : ''}">

                                                <div class="row">
                                                <div class="col-md-2">
                                                            <img src="${imagen}"
                                                                style="width:60px; height:60px; object-fit:cover; border-radius:8px;"
                                                                alt="producto">
                                                        </div>

                                                    <div class="col-md-6">
                                                        <h6 class="mb-1 fw-bold text-primary">
                                                            ${producto.nombre}
                                                        </h6>
                                                        <div class="small text-muted">
                                                            Marca:
                                                            <b>
                                                                ${producto.marca ?
                                    producto.marca.nombre :
                                    'SIN MARCA'}
                                                                    </b>
                                                                </div>
                                                                <div class="small">
                                                                    Cod Interno:
                                                                    ${producto.codigo_interno ?? '-'}
                                                                </div>
                                                                <div class="small">
                                                                    Nro Parte:
                                                                    ${producto.numero_parte_vehiculo ?? '-'}
                                                                </div>
                                                                <div class="small">
                                                                    Vehículo:
                                                                    ${producto.vehiculos_compatibles ?? '-'}
                                                                </div>
                                                                    <div class="small">
                                                                    Ubicaciòn:
                                                                    ${producto.ubicacion ?? '-'}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 text-end">
                                                                <span class="badge badge-success mb-2">
                                                                    Stock:
                                                                    ${producto.stock_actual}
                                                                </span>
                                                                <h5 class="text-success">
                                                                    Bs.
                                                                    ${producto.precio_venta}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                        });
                    }
                    $('#resultado_productos').html(html);
                    $('#resultado_productos').show();
                }
            });
        });


        $(document).on('click', '.producto-item', function () {

            productoSeleccionado = {
                id: $(this).data('id'),
                nombre: $(this).data('nombre'),
                precio_venta: $(this).data('precio'),
                precio_mayor: $(this).data('precio-mayor'),

                codigo_interno: $(this).data('codigo-interno'),
                marca: $(this).data('marca'),
                vehiculo: $(this).data('vehiculo'),
                ubicacion: $(this).data('ubicacion'),
                medida: $(this).data('medida'),
                imagen: $(this).data('imagen')
            };
            console.log('res', productoSeleccionado);
            $('#producto_id').val(productoSeleccionado.id);
            $('#buscar_producto').val(productoSeleccionado.nombre);

            precioNormalSeleccionado = productoSeleccionado.precio_venta;
            precioMayorSeleccionado = productoSeleccionado.precio_mayor;

            actualizarPrecio();
            calcularSubtotalPreview();
            $('#resultado_productos').hide();
        });


        function limpiarFormularioVenta() {
            productosVenta = [];
            recargarTabla();
            calcularTotal();
            $('#tabla_detalles').hide();
            $('#buscar_producto').val('');
            $('#producto_id').val('');
            $('#cantidad').val(1);
            $('#precio_venta').val('');
            $('#observacion').val('');
            $('#monto_pagado_recibo').val(0);
            $('#cambio_pagado_recibo').val(0);
            $('#monto_total_pagado_recibo').val(0);
            $('#tipo_pago_pagado_recibo')
                .val('')
                .prop('disabled', true);
            $('#realizo_pago_recibo')
                .prop('checked', false);
            $('#monto_pagado_recibo')
                .prop('readonly', true);
            $('#subtotal_preview').val('0.00');
            $('#detalle_venta').html('');

            $('#lista_pagos').html('');
            agregarPago();
            $('#cambio_visual').text('0.00');

            $('#imagen_cliente').attr('src', '/assets/img/default.jpg');
            $('#cliente_seleccionado').val('');
        }


        $('#cliente_seleccionado').on('change', function () {

            let imagen = $(this).find(':selected').data('imagen');
            let src = imagen
                ? `/storage/imagenesClientes/${imagen}`
                : `/assets/img/default.jpg`;

            $('#imagen_cliente').attr('src', src);
        });


        $(document).on('click', '.imagen-producto', function () {

            let src = $(this).attr('src');

            Swal.fire({
                imageUrl: src,
                imageWidth: 600,
                imageAlt: 'Producto',
                showConfirmButton: false,
                showCloseButton: true,
                width: '700px'
            });

        });

        //pagos divididos:

        function agregarPago() {
            let html = `
                                                    <div class="row pago-item mt-2">

                                                        <div class="col-md-4">
                                                            <select class="form-control form-control-sm metodo_pago">
                                                                <option value="EFECTIVO">EFECTIVO</option>
                                                                <option value="QR">QR</option>
                                                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <input type="number" class="form-control form-control-sm monto_pago" placeholder="Monto">
                                                        </div>

                                                       <div class="col-md-2">
                                                        <input type="number" id="descuento" class="form-control form-control-sm"  placeholder="Descuento">
                                                    </div>

                                                    </div>`;

            $('#lista_pagos').append(html);
        }

        function eliminarPago(btn) {
            $(btn).closest('.pago-item').remove();
        }

    </script>
    <style>
        .producto-item:hover {
            background: #f5f8fa;
        }

        .imagen-producto {
            cursor: zoom-in;
        }

        #imagenGrande {
            transition: transform 0.3s ease;
            cursor: zoom-in;
        }

        #imagenGrande:hover {
            transform: scale(1.8);
        }
    </style>
@endsection