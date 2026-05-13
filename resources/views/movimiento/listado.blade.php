<div class="modal fade" id="modalStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">CANTIDAD STOCK POR SUCURSAL DEL PRODUCTO <span class="text-info"
                        id="producto_nombre_modal"></span></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <div class="card-body" id="table_listado">
                </div>
            </div>

        </div>
    </div>
</div>


<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>

    function abrirStock(productoId, nombreProducto) {
        $('#producto_nombre_modal').text(nombreProducto);
        $('#modalStock').modal('show');

        $.ajax({
            url: "{{ route('movimiento.ajaxListado') }}",
            method: 'GET',
            data: { productoId: productoId },
            success: function (res) {
                if (res.estado) {
                    $('#table_listado').html(res.data.stock);
                } else {
                    $('#table_listado').html('<p class="text-danger text-center">Error al cargar el stock</p>');
                }
            },
            error: function () {
                $('#table_listado').html('<p class="text-danger text-center">Ocurrió un error en el servidor</p>');
            }
        });
    }

    function ajaxListado() {
        let datos = {};
        $.ajax({
            url: "{{ route('movimiento.ajaxListado') }}",
            method: "POST",
            data: {
                productoId: productoId
            },
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



    function abrirIngreso(productoId, sucursalId) {
        Swal.fire({
            title: 'Cantidad de Ingreso',
            input: 'number',
            inputAttributes: {
                min: 0.01,
                step: 0.01
            },
            showCancelButton: true,
            confirmButtonText: 'Agregar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/movimiento/ingreso', {
                    producto_id: productoId,
                    sucursal_id: sucursalId,
                    cantidad: result.value
                }, function (res) {
                    if (res.estado) {
                        Swal.fire('Éxito', res.mensaje, 'success');
                        abrirStock(productoId); // recarga la lista de stock
                    } else {
                        Swal.fire('Error', res.mensaje, 'error');
                    }
                });
            }
        });
    }

    function abrirEgreso(productoId, sucursalId) {
        Swal.fire({
            title: 'Cantidad de Egreso',
            input: 'number',
            inputAttributes: {
                min: 0.01,
                step: 0.01
            },
            showCancelButton: true,
            confirmButtonText: 'Agregar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/movimiento/egreso', {
                    producto_id: productoId,
                    sucursal_id: sucursalId,
                    cantidad: result.value
                }, function (res) {
                    if (res.estado) {
                        Swal.fire('Éxito', res.mensaje, 'success');
                        abrirStock(productoId); // recarga la lista de stock
                    } else {
                        Swal.fire('Error', res.mensaje, 'error');
                    }
                });
            }
        });
    }

</script>