<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Tipo precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>

            @foreach($venta->detalles as $detalle)

                <tr>
                    <td>
                        {{ $detalle->producto?->nombre }}
                    </td>
                    <td>
                        {{ $detalle->cantidad }}
                    </td>
                    <td>
                        {{ $detalle->tipo_precio }}
                    </td>
                    <td>
                        {{ number_format($detalle->subtotal, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>