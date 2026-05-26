<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajasController extends Controller
{
    public function listado()
    {
        return view('caja.listado');
    }

    public function ajaxListado()
    {
        $cajas = Caja::with([
            'usuario'
        ])
            ->orderBy('id', 'desc')
            ->get();
        $listado = view(
            'caja.ajaxListado',
            compact('cajas')
        )->render();
        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }

    public function abrirCaja(Request $request)
    {
        try {

            $request->validate([
                'monto_apertura' => 'required|numeric|min:0'
            ]);

            $usuario = Auth::user();
            $existeCaja = Caja::where('usuario_id', $usuario->id)
                ->where('estado', 'ABIERTA')
                ->first();
            if ($existeCaja) {
                return response()->json([
                    'estado' => false,
                    'mensaje' => 'Ya existe una caja abierta'
                ]);
            }

            $caja = new Caja();
            $caja->usuario_id = $usuario->id;
            $caja->sucursal_id = $usuario->sucursal_id;
            $caja->monto_apertura = $request->monto_apertura;
            $caja->total_ingresos = 0;
            $caja->total_egresos = 0;
            $caja->fecha_apertura = now();
            $caja->estado = 'ABIERTA';

            $caja->usuario_creador_id = $usuario->id;
            $caja->save();
            return response()->json([
                'estado' => true,
                'mensaje' => 'Caja abierta correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public function cerrarCaja(Request $request)
    {
        try {
            $usuario = Auth::user();
            $caja = Caja::find($request->caja_id);
            if (!$caja) {
                return response()->json([
                    'estado' => false,
                    'mensaje' => 'Caja no encontrada'
                ]);
            }
            $montoFinal =
                $caja->monto_apertura
                + $caja->total_ingresos
                - $caja->total_egresos;

            $caja->monto_cierre = $montoFinal;
            $caja->fecha_cierre = now();
            $caja->estado = 'CERRADA';
            $caja->usuario_modificador_id = $usuario->id;
            $caja->save();
            return response()->json([
                'estado' => true,
                'mensaje' => 'Caja cerrada correctamente'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}

