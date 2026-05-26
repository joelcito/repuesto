<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SucursalController extends Controller
{
    public function listado()
    {
        return view('sucursal.listado');
    }
}