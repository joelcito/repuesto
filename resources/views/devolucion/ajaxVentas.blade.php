<div style="overflow-x: auto;">

    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_ventas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>N° Venta</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Total</th>
                <th>Metodos de pago</th>
                <th>Productos</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->created_at }}</td>
                    <td>{{ $venta->cliente->nombres }}</td>
                    <td>{{ $venta->usuario->nombres }}</td>
                    <td>Bs {{ number_format($venta->total, 2) }}</td>
                    <td>{{ $venta->metodo_pago }}</td>
                    <td>{{ $venta->detalles->count() }}</td>
                    <td><button class="btn btn-primary btn-sm" onclick="seleccionarVenta({{ $venta->id }})">
                            Seleccionar
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>