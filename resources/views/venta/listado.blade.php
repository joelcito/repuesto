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
                    <h3 class="card-title fw-bold">Listado de venta</h3>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">
                    NUEVA VENTA
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="formularioVenta">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Cliente</label>
                            <select class="form-select" name="cliente_id">
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
                            <label>Método Pago</label>
                            <select class="form-select" name="metodo_pago" id="metodo_pago">
                                <option value="EFECTIVO">
                                    EFECTIVO
                                </option>
                                <option value="QR">
                                    QR
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Caja</label>
                            <select class="form-select" name="caja_id">
                                @foreach($cajas as $caja)
                                    <option value="{{ $caja->id }}">
                                        CAJA #{{ $caja->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Producto</label>
                            <select class="form-select" id="producto_id">
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" data-normal="{{ $producto->precio_venta }}"
                                        data-mayor="{{ $producto->precio_mayor }}">
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Cantidad</label>
                            <input type="number" id="cantidad" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label>Tipo Precio</label>
                            <select class="form-select" id="tipo_precio">
                                <option value="NORMAL">
                                    NORMAL
                                </option>
                                <option value="MAYOR">
                                    MAYOR
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success w-100" onclick="agregarProducto()">
                                Agregar
                            </button>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="detalle_venta">
                        </tbody>
                    </table>
                    <div class="text-end">
                        <h3>
                            TOTAL:
                            Bs.
                            <span id="total_general">
                                0.00
                            </span>
                        </h3>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="guardarVenta()">
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

            $('#producto_id').val('').trigger('change');

            $('#tipo_precio').val('NORMAL');

            $('#metodo_pago').val('EFECTIVO');

            $('#modalVenta').modal('show');
        }

        let productosVenta = [];
        function agregarProducto() {
            let select = $('#producto_id option:selected');
            let producto_id = select.val();
            let nombre = select.text();
            let cantidad = parseFloat($('#cantidad').val());
            let tipo_precio = $('#tipo_precio').val();

            if (tipo_precio == 'MAYOR') {
                precio = parseFloat(select.data('mayor'));
            } else {
                precio = parseFloat(select.data('normal'));
            }
            let subtotal = cantidad * precio;
            productosVenta.push({
                producto_id: producto_id,
                cantidad: cantidad,
                tipo_precio: tipo_precio
            });
            $('#detalle_venta').append(`
                                <tr>
                                    <td>${nombre}</td>
                                    <td>${cantidad}</td>
                                    <td>${precio}</td>
                                    <td>${subtotal}</td>
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

    </script>
@endsection