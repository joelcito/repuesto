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
                    <h3 class="card-title fw-bold">Listado de Venta</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevaVenta()">
                            <i class="fa fa-plus"></i> Nueva Venta
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


<div class="modal fade" id="modalVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-secondary text-white">
                <h3 class="fw-bold mb-0">
                    <i class="fa fa-shopping-cart me-2"></i>
                    NUEVA VENTA
                </h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioVenta">
                    <div class="card shadow-sm mb-3">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="fw-semibold fs-6 mb-2">
                                        Cliente
                                    </label>
                                    <select class="form-select form-select-sm" name="cliente_id">
                                        <option value="">
                                            Seleccione
                                        </option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">
                                                {{ $cliente->nombres }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold fs-6 mb-2">
                                        Método Pago
                                    </label>
                                    <select class="form-select form-select-sm" name="metodo_pago" id="metodo_pago">
                                        <option value="EFECTIVO">
                                            EFECTIVO
                                        </option>
                                        <option value="QR">
                                            QR
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold fs-6 mb-2">
                                        Caja
                                    </label>
                                    <select class="form-select form-select-sm" name="caja_id">
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
                    <div class="card shadow-sm mb-4">

                        <div class="card-body">
                            <div class="row g-3 align-items-end">
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

                                <div class="col-md-3">
                                    <button type="button" class="btn btn-success btn-sm w-100"
                                        onclick="agregarProducto()">
                                        <i class="fa fa-plus me-1"></i>
                                        Agregar Producto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DETALLE -->
                    <div class="card shadow-sm">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr class="fw-bold text-center">
                                            <th>Producto</th>
                                            <th width="120">Cantidad</th>
                                            <th width="140">Precio</th>
                                            <th width="140">Subtotal</th>
                                            <th width="80">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle_venta">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                No hay productos agregados
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- TOTAL -->
                            <div class="d-flex justify-content-end mt-4">
                                <div class="bg-light-primary px-5 py-3 rounded">
                                    <h2 class="fw-bold mb-0">
                                        TOTAL:
                                        <span class="text-primary">
                                            Bs. <span id="total_general">0.00</span>
                                        </span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button class="btn btn-primary" onclick="guardarVenta()">
                    <i class="fa fa-save me-1"></i>
                    Guardar Venta
                </button>
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

        $('#producto_id').change(function () {
            actualizarPrecio();
        });

        $('#tipo_precio').change(function () {
            actualizarPrecio();
        });

        function ajaxListado() {
            $.ajax({
                url: "{{ route('venta.ajaxListado') }}",
                method: "POST",
                success: function (resultado) {
                    if (resultado.estado) {
                        $('#table_listado')
                            .html(resultado.data.listado);
                    }
                }
            });
        }


        function modalNuevaVenta() {
            productosVenta = [];
            $('#detalle_venta').html('');
            $('#total_general').text('0.00');
            $('#cantidad').val(1);
            $('#cliente_id').val('').trigger('change');
            $('#producto_id').prop('selectedIndex', 0);
            $('#tipo_precio').val('NORMAL');
            $('#metodo_pago').val('EFECTIVO');
            actualizarPrecio();
            $('#modalVenta').modal('show');
        }

        let productosVenta = [];


        function agregarProducto() {
            let select = $('#producto_id option:selected');
            let producto_id = select.val();
            let nombre = select.text();
            let cantidad = parseFloat($('#cantidad').val());
            let tipo_precio = $('#tipo_precio').val();
            let precio = parseFloat($('#precio_venta').val());
            let subtotal = cantidad * precio;
            productosVenta.push({
                producto_id: producto_id,
                cantidad: cantidad,
                tipo_precio: tipo_precio,
                precio: precio
            });
            $('#detalle_venta').append(`
                    <tr>
                        <td>${nombre}</td>
                        <td>${cantidad}</td>
                        <td>${precio.toFixed(2)}</td>
                        <td>${subtotal.toFixed(2)}</td>
                        <td>
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            onclick="eliminarFila(this, ${producto_id})">
                            X
                        </button>
                    </td>
                    </tr>
                `);
            calcularTotal();
        }

        function calcularTotal() {
            let total = 0;
            $('#detalle_venta tr').each(function () {
                let subtotal = parseFloat(
                    $(this).find('td:eq(3)').text()
                ) || 0;
                total += subtotal;
            });
            $('#total_general').text(
                total.toFixed(2)
            );
        }

        function eliminarFila(boton, producto_id) {
            productosVenta = productosVenta.filter(item =>
                item.producto_id != producto_id
            );
            $(boton).closest('tr').remove();
            calcularTotal();
        }

        function guardarVenta() {
            if (productosVenta.length == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Agregue productos'
                });
                return;
            }
            let datos = {
                cliente_id:
                    $('[name="cliente_id"]').val(),
                metodo_pago:
                    $('[name="metodo_pago"]').val(),
                caja_id:
                    $('[name="caja_id"]').val(),
                productos:
                    productosVenta
            };

            $.ajax({
                url: "{{ route('venta.guardarVenta') }}",
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
                        $('#modalVenta').modal('hide');
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


        function actualizarPrecio() {
            let producto = $('#producto_id option:selected');
            let tipo_precio = $('#tipo_precio').val();
            let precio = 0;
            if (tipo_precio == 'MAYOR') {
                precio = producto.data('mayor');
            } else {
                precio = producto.data('normal');
            }
            $('#precio_venta').val(parseFloat(precio).toFixed(2));
        }
    </script>
@endsection