@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/jquery.orgchart.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
    <style>
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <!--begin::Card-->
            <div class="card">
                <div class="card-body py-4">
                    <!--begin::Details-->
                    <div class="d-flex mb-9">
                        <!--begin: Pic-->
                        <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                            @if ($cliente->imagen)
                                <div style="width: 200px; height: 200px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <img width="100%" height="100%"
                                                src="{{ asset('storage/imagenesClientes') }}/{{ $cliente->imagen }}"
                                                height="110" alt="image">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <img src="{{ asset('assets/img/default.jpg') }}" height="110" alt="image">
                            @endif
                        </div>
                        <div class="flex-grow-1" style="margin-left: 10px;">
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between flex-wrap mt-1">
                                <div class="d-flex mr-3">
                                    <h2><span class="text-primary">CLIENTE: </span> {{ $cliente->nombres . "
                                        " . $cliente->ap_paterno . " " . $cliente->ap_materno }}</h2>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-3">
                                    <h6><span class="text-primary">CELULAR: </span>
                                        {{ $cliente->celular }}
                                    </h6>
                                </div>

                                <div class="col-md-3">
                                    <h6><span class="text-primary">CEDULA: </span>
                                        {{ $cliente->cedula }}
                                    </h6>
                                </div>

                                <div class="col-md-3">
                                    <h6><span class="text-primary">NIT: </span>
                                        {{ $cliente->nit }}
                                    </h6>
                                </div>

                                <div class="col-md-3">
                                    <h6><span class="text-primary">RAZON SOCIAL: </span>
                                        {{ $cliente->razon_social }}
                                    </h6>
                                </div>
                            </div>

                            <hr />

                            <div class="row">
                                <div class="col-md-12">
                                    <h6><span class="text-primary">DIRECCION: </span>
                                        {{ $cliente->direccion }}
                                    </h6>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-2">
                                    <h6><span class="text-primary">REFERENCIA 1: </span>
                                        {{ $cliente->nombre_referencia_1 }}
                                    </h6>
                                </div>

                                <div class="col-md-2">
                                    <h6><span class="text-primary">CELULAR 1: </span>
                                        {{ $cliente->celular_referencia_1 }}
                                    </h6>
                                </div>

                                <div class="col-md-2">
                                    <h6><span class="text-primary">REFERENCIA 2: </span>
                                        {{ $cliente->nombre_referencia_1 }}
                                    </h6>
                                </div>

                                <div class="col-md-2">
                                    <h6><span class="text-primary">CELULAR 2: </span>
                                        {{ $cliente->celular_referencia_1 }}
                                    </h6>
                                </div>

                                <div class="col-md-2">
                                    <h6><span class="text-primary">REFERENCIA 3: </span>
                                        {{ $cliente->nombre_referencia_1 }}
                                    </h6>
                                </div>

                                <div class="col-md-2">
                                    <h6><span class="text-primary">CELULAR 3: </span>
                                        {{ $cliente->celular_referencia_1 }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator separator-solid"></div>
                    <form action="{{ route('reporte.cuentaPorCobrarRango') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <input type="number" class="form-control form-control-sm"
                                    placeholder="Nro de factura Inicial para cobrar..." name="nro_inicia_factura">
                                <input type="hidden" value="{{ $cliente->id }}" name="cliente_id_reporte_cobrar">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control form-control-sm"
                                    placeholder="Nro de factura Final para cobrar..." name="nro_fin_factura">
                            </div>
                            <div class="col-md-8">
                                <button class="btn btn-danger w-100 btn-sm"><i class="fa fa-file-pdf"></i> Descargar
                                    Reporte</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-md-12">

        @php
            $facturasValidas = $facturas->whereNull('estado_venta');
            $facturasInValidas = $facturas->where('estado_venta', 'ARCHIVADO');
        @endphp

        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxlg">
                    <div class="card shadow-sm">
                        <div class="card-body py-4">

                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_7">Orden de
                                        Recepcion / Facturas ACTIVAS</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_8">Orden de Recepcion /
                                        Facturas ARCHIVADOS</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="kt_tab_pane_7" role="tabpanel">
                                    <!--begin::Accordion-->
                                    <div class="accordion accordion-icon-collapse" id="kt_accordion_3">

                                        @foreach ($facturasValidas as $keyPri => $facturaValida)
                                            <!--begin::Item-->
                                            <div class="mb-5">
                                                <!--begin::Header-->
                                                <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse"
                                                    data-bs-target="#kt_accordion_3_item_1_{{ $keyPri }}">
                                                    <span class="accordion-icon">
                                                        <i class="ki-duotone ki-plus-square fs-3 accordion-icon-off"><span
                                                                class="path1"></span><span class="path2"></span><span
                                                                class="path3"></span></i>
                                                        <i class="ki-duotone ki-minus-square fs-3 accordion-icon-on"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </span>
                                                    <h3 class="fs-4 fw-semibold mb-0 ms-4">
                                                        Or. Rec. / Fac : <span
                                                            class="badge badge-success">{{ $facturaValida->numero_factura }}</span>
                                                        | Fecha: <span
                                                            class="badge badge-success">{{ $facturaValida->fecha }}</span>
                                                        | Est. Pag.: <span
                                                            class="badge badge-{{ $facturaValida->estado_pago == 'DEUDA' ? 'danger' : 'success'}}">{{ $facturaValida->estado_pago }}</span>
                                                        | Prioridad: <span
                                                            class="badge badge-success">{{ $facturaValida->prioridad }}</span>
                                                        | <button title="Enviar a Archivar"
                                                            class="btn btn-icon btn-circle btn-sm btn-primary"
                                                            onclick="enviarArchivar({{ $facturaValida->id }}, {{ $facturaValida->numero_factura }})"><i
                                                                class="fa fa-right-long"></i></button>
                                                    </h3>
                                                </div>
                                                <!--end::Header-->

                                                <!--begin::Body-->
                                                <div id="kt_accordion_3_item_1_{{ $keyPri }}"
                                                    class="fs-6 collapse {{ $keyPri == 0 ? 'show' : '' }} ps-10"
                                                    data-bs-parent="#kt_accordion_3">
                                                    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab"
                                                                href="#kt_tab_pane_7_ots_{{ $keyPri }}">OT'S</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab"
                                                                href="#kt_tab_pane_8_pagos_{{ $keyPri }}">PAGOS</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content" id="myTabContent">

                                                        <div class="tab-pane fade show active"
                                                            id="tab_repuestos_{{ $keyPri }}">
                                                            fdgfdgfdgfdg
                                                        </div>

                                                        <div class="tab-pane fade" id="tab_lubricantes_{{ $keyPri }}">
                                                            dfgfdfgdfdgfgd
                                                        </div>

                                                        <div class="tab-pane fade" id="tab_total_{{ $keyPri }}">
                                                            dfdgfdgfdg
                                                        </div>
                                                        <div class="tab-pane fade show active"
                                                            id="kt_tab_pane_7_ots_{{ $keyPri }}" role="tabpanel">


                                                            <div class="tab-pane fade"
                                                                id="kt_tab_pane_8_pagos_{{ $keyPri }}" role="tabpanel">
                                                                @php
                                                                    $pagos = $facturaValida->pagos;
                                                                @endphp
                                                                <div style="overflow-x: auto;">
                                                                    <!--begin::Table-->
                                                                    <table
                                                                        class="table align-middle table-row-dashed fs-7 gy-4"
                                                                        id="kt_table_users">
                                                                        <thead>
                                                                            <tr
                                                                                class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                                                                <th>Sucursal</th>
                                                                                <th></th>
                                                                                <th>Fecha</th>
                                                                                <th>Descripcion</th>
                                                                                <th>Tipo Pago</th>
                                                                                <th width="10px">Monto Efectivo</th>
                                                                                <th width="10px">Monto Deposito</th>
                                                                                <th>Estado</th>
                                                                                <th>Usuario</th>
                                                                                <th>Accion</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="text-gray-600 fw-semibold">
                                                                            @php
                                                                                $totalEfectivo = 0;
                                                                                $totalTramsferencia = 0;
                                                                                $totalQR = 0;
                                                                                $totalVenta = 0;
                                                                                $totalSalida = 0;
                                                                                $totalOtrosIngresos = 0;
                                                                                $totalRecaudado = 0;
                                                                            @endphp
                                                                            @forelse ($pagos as $pago)
                                                                                <tr class="{{ 'bg-light-warning' }}">
                                                                                    <td>{{ $pago?->sucursal?->nombre }}</td>
                                                                                    <td></td>
                                                                                    <td>{{ $pago->fecha }}</td>
                                                                                    <td>{{ $pago->descripcion }}</td>
                                                                                    <td>{{ $pago->tipo_pago }}</td>
                                                                                    <td>
                                                                                        @if ($pago->estado === 'INGRESO')
                                                                                            @if ($pago->tipo_pago === 'EFECTIVO')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @elseif($pago->estado === 'SALIDA')
                                                                                            @if ($pago->tipo_pago === 'EFECTIVO')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($pago->estado === 'INGRESO')
                                                                                            @if ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @elseif($pago->estado === 'SALIDA')
                                                                                            @if ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($pago->estado === 'INGRESO')
                                                                                            <span
                                                                                                class="badge badge-success">{{ $pago->estado }}</span>
                                                                                        @else
                                                                                            <span
                                                                                                class="badge badge-danger">{{ $pago->estado }}</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>{{ $pago->usuario->name }}</td>
                                                                                    <td>
                                                                                        <a target="_blank"
                                                                                            href="{{url('pago/comprobantePago', [$pago->id])}}"
                                                                                            class="btn btn-icon btn-info btn-sm"
                                                                                            title="Imprimir Comprobante"><i
                                                                                                class="fa fa-file"></i></a>
                                                                                    </td>
                                                                                </tr>
                                                                                @php
                                                                                    if ($pago->estado === 'INGRESO') {
                                                                                        if ($pago->tipo_pago === 'EFECTIVO') {
                                                                                            $totalEfectivo = $totalEfectivo + $pago->monto;
                                                                                            if (is_null($pago->factura_id)) {
                                                                                                $totalOtrosIngresos = $totalOtrosIngresos + $pago->monto;
                                                                                            }
                                                                                        } else if ($pago->tipo_pago === 'QR') {
                                                                                            $totalQR = $totalQR + $pago->monto;
                                                                                        } else if ($pago->tipo_pago === 'TRANSFERENCIA') {
                                                                                            $totalTramsferencia = $totalTramsferencia + $pago->monto;
                                                                                        }

                                                                                        if ($pago->factura_id) {
                                                                                            $totalVenta = $totalVenta + $pago->monto;
                                                                                        }

                                                                                        $totalRecaudado = $totalRecaudado + $pago->monto;
                                                                                    } elseif ($pago->estado === 'SALIDA') {
                                                                                        $totalSalida = $totalSalida + $pago->monto;
                                                                                    }

                                                                                @endphp
                                                                            @empty
                                                                                <h4 class="text-danger">No hay datos</h4>
                                                                            @endforelse
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr class="bg-light-dark">
                                                                                <th class="text-center" colspan="3"><b>TOTAL
                                                                                        RECAUDADO</b></th>
                                                                                <th class="text-center" colspan="2">
                                                                                    {{ number_format(($totalRecaudado - $totalSalida), 2) }}
                                                                                </th>
                                                                                <th class="text-center" colspan="3"><b>TOTAL
                                                                                        VENTA</b></th>
                                                                                <th class="text-center" colspan="2">
                                                                                    {{ number_format(($totalVenta), 2) }}
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                    <!--end::Table-->
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Body-->
                                                </div>
                                                <!--end::Item-->
                                        @endforeach
                                        </div>
                                        <!--end::Accordion-->
                                    </div>
                                    <div class="tab-pane fade" id="kt_tab_pane_8" role="tabpanel">
                                        <!--begin::Accordion-->
                                        <div class="accordion accordion-icon-collapse" id="kt_accordion_34">

                                            @foreach ($facturasInValidas as $key => $facturaInValida)
                                                <!--begin::Item-->
                                                <div class="mb-5">
                                                    <!--begin::Header-->
                                                    <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse"
                                                        data-bs-target="#kt_accordion_3_item_2_{{ $key }}">
                                                        <span class="accordion-icon">
                                                            <i class="ki-duotone ki-plus-square fs-3 accordion-icon-off"><span
                                                                    class="path1"></span><span class="path2"></span><span
                                                                    class="path3"></span></i>
                                                            <i class="ki-duotone ki-minus-square fs-3 accordion-icon-on"><span
                                                                    class="path1"></span><span class="path2"></span></i>
                                                        </span>
                                                        <h3 class="fs-4 fw-semibold mb-0 ms-4">
                                                            Or. Rec. / Fac : <span
                                                                class="badge badge-success">{{ $facturaInValida->numero_factura }}</span>
                                                            | Fecha: <span
                                                                class="badge badge-success">{{ $facturaInValida->fecha }}</span>
                                                            | Est. Pag.: <span
                                                                class="badge badge-{{ $facturaInValida->estado_pago == 'DEUDA' ? 'danger' : 'success'}}">{{ $facturaInValida->estado_pago }}</span>
                                                            | Prioridad: <span
                                                                class="badge badge-success">{{ $facturaInValida->prioridad }}</span>
                                                        </h3>
                                                    </div>
                                                    <!--end::Header-->

                                                    <!--begin::Body-->
                                                    <div id="kt_accordion_3_item_2_{{ $key }}"
                                                        class="fs-6 collapse {{ $key == 0 ? 'show' : '' }} ps-10"
                                                        data-bs-parent="#kt_accordion_34">
                                                        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-bs-toggle="tab"
                                                                    href="#kt_tab_pane_7_ots_2_{{ $key }}">OT'S</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-bs-toggle="tab"
                                                                    href="#kt_tab_pane_8_pagos_2_{{ $key }}">PAGOS</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content" id="myTabContent">

                                                            <div class="tab-pane fade" id="kt_tab_pane_8_pagos_2_{{ $key }}"
                                                                role="tabpanel">
                                                                @php
                                                                    $pagos = $facturaInValida->pagos;
                                                                @endphp
                                                                <div style="overflow-x: auto;">
                                                                    <!--begin::Table-->
                                                                    <table
                                                                        class="table align-middle table-row-dashed fs-7 gy-4"
                                                                        id="kt_table_users">
                                                                        <thead>
                                                                            <tr
                                                                                class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                                                                <th>Sucursal</th>
                                                                                <th></th>
                                                                                <th>Fecha</th>
                                                                                <th>Descripcion</th>
                                                                                <th>Tipo Pago</th>
                                                                                <th width="10px">Monto Efectivo</th>
                                                                                <th width="10px">Monto Deposito</th>
                                                                                <th>Estado</th>
                                                                                <th>Usuario</th>
                                                                                <th>Accion</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="text-gray-600 fw-semibold">
                                                                            @php
                                                                                $totalEfectivo = 0;
                                                                                $totalTramsferencia = 0;
                                                                                $totalQR = 0;
                                                                                $totalVenta = 0;
                                                                                $totalSalida = 0;
                                                                                $totalOtrosIngresos = 0;
                                                                                $totalRecaudado = 0;
                                                                            @endphp
                                                                            @forelse ($pagos as $pago)
                                                                                <tr class="{{ 'bg-light-warning' }}">
                                                                                    <td>{{ $pago?->sucursal?->nombre }}</td>
                                                                                    <td></td>
                                                                                    <td>{{ $pago->fecha }}</td>
                                                                                    <td>{{ $pago->descripcion }}</td>
                                                                                    <td>{{ $pago->tipo_pago }}</td>
                                                                                    <td>
                                                                                        @if ($pago->estado === 'INGRESO')
                                                                                            @if ($pago->tipo_pago === 'EFECTIVO')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @elseif($pago->estado === 'SALIDA')
                                                                                            @if ($pago->tipo_pago === 'EFECTIVO')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($pago->estado === 'INGRESO')
                                                                                            @if ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @elseif($pago->estado === 'SALIDA')
                                                                                            @if ($pago->tipo_pago === 'TRANSFERENCIA' || $pago->tipo_pago === 'QR')
                                                                                                {{ $pago->monto }}
                                                                                            @else
                                                                                                0.00
                                                                                            @endif
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($pago->estado === 'INGRESO')
                                                                                            <span
                                                                                                class="badge badge-success">{{ $pago->estado }}</span>
                                                                                        @else
                                                                                            <span
                                                                                                class="badge badge-danger">{{ $pago->estado }}</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>{{ $pago->usuario->name }}</td>
                                                                                    <td>
                                                                                        <a target="_blank"
                                                                                            href="{{url('pago/comprobantePago', [$pago->id])}}"
                                                                                            class="btn btn-icon btn-info btn-sm"
                                                                                            title="Imprimir Comprobante"><i
                                                                                                class="fa fa-file"></i></a>
                                                                                    </td>
                                                                                </tr>
                                                                                @php
                                                                                    if ($pago->estado === 'INGRESO') {
                                                                                        if ($pago->tipo_pago === 'EFECTIVO') {
                                                                                            $totalEfectivo = $totalEfectivo + $pago->monto;
                                                                                            if (is_null($pago->factura_id)) {
                                                                                                $totalOtrosIngresos = $totalOtrosIngresos + $pago->monto;
                                                                                            }
                                                                                        } else if ($pago->tipo_pago === 'QR') {
                                                                                            $totalQR = $totalQR + $pago->monto;
                                                                                        } else if ($pago->tipo_pago === 'TRANSFERENCIA') {
                                                                                            $totalTramsferencia = $totalTramsferencia + $pago->monto;
                                                                                        }

                                                                                        if ($pago->factura_id) {
                                                                                            $totalVenta = $totalVenta + $pago->monto;
                                                                                        }

                                                                                        $totalRecaudado = $totalRecaudado + $pago->monto;
                                                                                    } elseif ($pago->estado === 'SALIDA') {
                                                                                        $totalSalida = $totalSalida + $pago->monto;
                                                                                    }

                                                                                @endphp
                                                                            @empty
                                                                                <h4 class="text-danger">No hay datos</h4>
                                                                            @endforelse
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr class="bg-light-dark">
                                                                                <th class="text-center" colspan="3"><b>TOTAL
                                                                                        RECAUDADO</b></th>
                                                                                <th class="text-center" colspan="2">
                                                                                    {{ number_format(($totalRecaudado - $totalSalida), 2) }}
                                                                                </th>
                                                                                <th class="text-center" colspan="3"><b>TOTAL
                                                                                        VENTA</b></th>
                                                                                <th class="text-center" colspan="2">
                                                                                    {{ number_format(($totalVenta), 2) }}
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                    <!--end::Table-->
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Body-->
                                                </div>
                                                <!--end::Item-->
                                            @endforeach
                                        </div>
                                        <!--end::Accordion-->
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
        <script src="{{ asset('assets/js/jquery.orgchart.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
            $.ajaxSetup({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            function enviarArchivar(factura, nro) {
                Swal.fire({
                    title: "Esta seguro de enviar a Archivado la venta Nro: " + nro + "?",
                    text: "Ya no podras revertir eso!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, enviar!"
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: "{{ route('factura.enviarArchivar') }}",
                            method: "POST",
                            data: { factura: factura },
                            success: function (resultado) {
                                if (resultado.estado) {
                                    Swal.fire(
                                        'Éxito',
                                        resultado.mensaje,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error',
                                        resultado.mensaje,
                                        'error'
                                    );
                                }
                            }
                        })

                    }
                });

            }

        </script>
    @endsection