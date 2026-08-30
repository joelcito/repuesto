@extends('layouts.app')

@section('css')
@endsection

@section('content')

@if(auth()->user()->esAdministrador())
    <div class="row g-5 gx-xl-10 mb-5">
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#F1416C;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-box fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        {{ $totalProductos ?? 0 }}
                    </div>

                    <div class="text-white fw-semibold">
                        Productos
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#4d41f1;">
                <div class="card-body text-center py-10">
                    <i class="fa fa-shopping-cart fs-2x text-white mb-5"></i>
                    <div class="fs-2hx fw-bold text-white">
                        Bs {{ number_format($ventasHoy ?? 0, 2) }}
                    </div>
                    <div class="text-white fw-semibold">
                        Ventas Hoy
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#f18241;">
                <div class="card-body text-center py-10">
                    <i class="fa fa-chart-line fs-2x text-white mb-5"></i>
                    <div class="fs-2hx fw-bold text-white">
                        Bs {{ number_format($utilidades ?? 0, 2) }}
                    </div>
                    <div class="text-white fw-semibold">
                        Utilidades
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#3e7213;">
                <div class="card-body text-center py-10">
                    <i class="fa fa-users fs-2x text-white mb-5"></i>
                    <div class="fs-2hx fw-bold text-white">
                        {{ $totalUsuarios ?? 0 }}
                    </div>
                    <div class="text-white fw-semibold">
                        Usuarios
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-5">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        Productos con stock mínimo
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3 mb-0">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="ps-5">Producto</th>
                                    <th class="text-center">Stock actual</th>
                                    <th class="text-center pe-5">Mínimo</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($productos ?? [] as $producto)
                                    <tr>
                                        <td class="ps-5">
                                            <span class="fw-bold text-gray-800">
                                                {{ $producto->nombre }}
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge badge-light-danger fs-7">
                                                {{ $producto->stock_actual_calculado }}
                                            </span>
                                        </td>

                                        <td class="text-center pe-5">
                                            <span class="badge badge-light-warning fs-7">
                                                {{ $producto->stock_minimo }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-10 text-muted">
                                            No hay productos con stock mínimo
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- ULTIMAS VENTAS --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Últimas Ventas
                    </h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-row-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasVentas ?? [] as $venta)
                                <tr>
                                    <td>
                                        {{ $venta->id }}
                                    </td>
                                    <td>
                                        {{ $venta->cliente->razon_social ?? 'Sin cliente' }}
                                    </td>
                                    <td>
                                        Bs {{ number_format($venta->total, 2) }}
                                    </td>
                                    <td>
                                        {{ $venta->created_at?->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Sin registros
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFICO --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Ventas Mensuales
                    </h3>
                </div>
                <div class="card-body">
                    <div id="graficoVentas" style="width:100%; height:400px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- ========================================================= --}}
{{-- DASHBOARD OPERADOR / VENTAS / ALMACEN --}}
{{-- ========================================================= --}}
@if(
        auth()->user()->esOperador()
        || auth()->user()->esVentas()
        || auth()->user()->esAlmacen()
    )

    <div class="row g-5 gx-xl-10 mb-5">
        <div class="col-md-4">
            <div class="card card-flush" style="background-color:#F1416C;">
                <div class="card-body text-center py-10">
                    <i class="fa fa-box fs-2x text-white mb-5"></i>
                    <div class="fs-2hx fw-bold text-white">
                        {{ $totalProductos ?? 0 }}
                    </div>
                    <div class="text-white fw-semibold">
                        Productos
                    </div>
                </div>
            </div>
        </div>
        @if(
                auth()->user()->esOperador()
                || auth()->user()->esVentas()
            )

            <div class="col-md-4">
                <div class="card card-flush" style="background-color:#4d41f1;">
                    <div class="card-body text-center py-10">
                        <i class="fa fa-shopping-cart fs-2x text-white mb-5"></i>
                        <div class="fs-2hx fw-bold text-white">
                            Bs {{ number_format($ventasHoy ?? 0, 2) }}
                        </div>
                        <div class="text-white fw-semibold">
                            Ventas Hoy
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- STOCK BAJO --}}



    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        Productos con stock minimo
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3 mb-0">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="ps-5">Producto</th>
                                    <th class="text-center">Stock actual</th>
                                    <th class="text-center pe-5">Mínimo</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($productos ?? [] as $producto)
                                    <tr>
                                        <td class="ps-5">
                                            <span class="fw-bold text-gray-800">
                                                {{ $producto->nombre }}
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge badge-light-danger fs-7">
                                                {{ $producto->stock_actual_calculado }}
                                            </span>
                                        </td>

                                        <td class="text-center pe-5">
                                            <span class="badge badge-light-warning fs-7">
                                                {{ $producto->stock_minimo }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-10 text-muted">
                                            No hay productos con stock mínimo
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if(
                auth()->user()->esOperador()
                || auth()->user()->esVentas()
            )
        @endif
    </div>
@endif
@stop

@section('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js">
    </script>
    <script>
        google.charts.load('current', {
            packages: ['corechart']
        });

        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Mes', 'Ventas'],
                @foreach($ventasMensuales ?? [] as $venta)
                    ['{{ $venta->mes }}', {{ $venta->total }}],
                @endforeach
                                                                            ]);

            var options = {
                title: 'Ventas Mensuales',
                curveType: 'function',
                legend: {
                    position: 'bottom'
                }
            };
            var chart = new google.visualization.LineChart(
                document.getElementById('graficoVentas')
            );
            chart.draw(data, options);
        }
    </script>
@endsection