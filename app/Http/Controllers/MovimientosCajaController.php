<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MovimientoCaja;

class MovimientoCajaController extends Controller
{
    public function listado()
    {
        return view('sucursal.listado');
    }

    public function guardarMovimiento(Request $request)
    {
        try {

            $usuario = Auth::user();

            MovimientoCaja::create([

                'caja_id' => $request->caja_id,

                'venta_id' => $request->venta_id,

                'tipo' => $request->tipo,

                'metodo_pago' => $request->metodo_pago,

                'monto' => $request->monto,

                'descripcion' => $request->descripcion,

                'fecha' => now(),

                'estado' => 'ACTIVO',

                'usuario_creador_id' => $usuario->id
            ]);

            return response()->json([
                'estado' => true,
                'mensaje' => 'Movimiento registrado correctamente'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}
