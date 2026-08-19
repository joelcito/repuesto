```blade
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Ultimos Productos
                    </h3>
                </div>
                <div class="card-body">
                    @forelse($ultimosProductos ?? [] as $producto)
                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                {{ $producto->nombre }}
                            </span>
                            <span class="badge badge-primary">
                                {{ $producto->stock_actual }}
                            </span>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            Sin productos
                        </div>
                    @endforelse
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
        <div class="col-md-4">
            <div class="card card-flush" style="background-color:#f18241;">
                <div class="card-body text-center py-10">
                    <i class="fa fa-exclamation-triangle fs-2x text-white mb-5"></i>
                    <div class="fs-2hx fw-bold text-white">
                        {{ $productosStockBajo ?? 0 }}
                    </div>
                    <div class="text-white fw-semibold">
                        Stock Bajo
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Últimos Productos
                    </h3>
                </div>
                <div class="card-body">
                    @forelse($ultimosProductos ?? [] as $producto)
                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                {{ $producto->nombre }}
                            </span>
                            <span class="badge badge-primary">
                                {{ $producto->stock_actual }}
                            </span>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            Sin productos
                        </div>
                    @endforelse
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