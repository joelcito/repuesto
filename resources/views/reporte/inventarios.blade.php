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
                        <h3 class="card-title fw-bold">REPORTE DE INVENTARIOS</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('reporte.inventarios.pdf') }}" method="POST" target="_blank">
                            @csrf
                            <button class="btn btn-danger">
                                <i class="fa fa-file-pdf"></i>
                                Generar PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection