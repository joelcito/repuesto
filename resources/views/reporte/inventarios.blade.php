```blade
@extends('layouts.app')

@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxlg">

                <div class="card shadow-sm">

                    <div class="card-header bg-light-info py-4">
                        <h3 class="card-title fw-bold">
                            REPORTE DE INVENTARIOS
                        </h3>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('reporte.inventarios.pdf') }}" method="POST" target="_blank">

                            @csrf

                            <div class="row">

                                {{-- Sucursal --}}
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Sucursal
                                    </label>

                                    <select name="sucursal_id" class="form-select" required>

                                        <option value="">
                                            Seleccione una sucursal
                                        </option>

                                        @foreach($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}">
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Tipo de producto --}}
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Tipo de producto
                                    </label>

                                    <select name="tipo_producto" class="form-select">

                                        <option value="TODOS">
                                            Todos
                                        </option>

                                        <option value="REPUESTO">
                                            Repuestos
                                        </option>

                                        <option value="LUBRICANTE">
                                            Lubricantes
                                        </option>

                                    </select>
                                </div>

                                {{-- Botón --}}
                                <div class="col-md-2 d-flex align-items-end">

                                    <button type="submit" class="btn btn-danger w-100">

                                        <i class="fa fa-file-pdf"></i>
                                        Generar PDF

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
```