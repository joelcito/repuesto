<form id="formularioDescuentoAdicional">
    <input type="hidden" name="venta_id" id="venta_id" value="{{ $venta->id }}">
    <div class="row">
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold form-label mb-2 ">Sucursal</label>
                <input type="text" class="form-control form-control-sm" id="sucursal" name="sucursal"
                    value="{{ optional($venta->sucursal)->nombre }}" @readonly(true) disabled>
                <div class="text-danger error-message" id="error-sucursal"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold form-label mb-2 ">Cliente</label>
                <input type="text" class="form-control form-control-sm" id="cliente" name="cliente"
                    value="{{ optional($venta->cliente)->nombres . ' ' . optional($venta->cliente)->ap_paterno . ' ' . optional($venta->cliente)->ap_materno }}"
                    @readonly(true) disabled>
                <div class="text-danger error-message" id="error-cliente"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">N° Fac/Rec</label>
                <input type="number" class="form-control form-control-sm" id="numero" name="numero"
                    value="{{ sprintf('%06d', $venta->numero_venta) }}" @readonly(true) disabled>
                <div class="text-info" id="mensaje-numero"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">Total</label>
                <input type="number" class="form-control form-control-sm" id="total" name="total"
                    value="{{ $venta->total }}" @readonly(true) disabled>
                <div class="text-info" id="mensaje-total"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">Pagado</label>
                <input type="number" class="form-control form-control-sm" id="pagado" name="pagado"
                    value="{{ $pagado }}" @readonly(true) disabled>
                <div class="text-info" id="mensaje-pagado"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">Saldo</label>
                <input type="number" class="form-control form-control-sm" id="saldo" name="saldo"
                    value="{{ $venta->total - $pagado }}" @readonly(true)>
                <div class="text-info" id="mensaje-saldo"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2 required">Descuento Adicional</label>
                <input type="number" min="0" max="{{ $venta->total - $pagado }}" step="0.01"
                    class="form-control form-control-sm" value="{{ $venta->descuento_adicional }}" required
                    name="descuento_adicional" id="descuento_adicional">
            </div>
        </div>
        <div class="col-md-8">
            <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2 required">Descripcion de rebaja</label>
                <textarea class="form-control" name="descripcion_descuento_adicional"
                    id="descripcion_descuento_adicional" cols="30" rows="10">{{ $venta->descripcion }}</textarea>
            </div>
        </div>
    </div>
</form>