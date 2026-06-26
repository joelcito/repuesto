<?php

namespace App\Http\Controllers;

use App\Models\ClienteVehiculo;
use Auth;
use DB;
use Illuminate\Http\Request;

class ClienteVehiculoController extends Controller
{
    public function guardarVehiculo(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'usuario_cliente_id' => 'required',
                'nombre_vehiculo' => 'required',
                'modelo' => 'required',
            ]);
            $usuario = Auth::user();
            if ($request->vehiculo_id == 0) {
                $vehiculo = new ClienteVehiculo();
                $vehiculo->usuario_creador_id = $usuario->id;
            } else {
                $vehiculo = ClienteVehiculo::find($request->vehiculo_id);
                $vehiculo->usuario_modificador_id = $usuario->id;
            }

            $vehiculo->usuario_cliente_id = $request->usuario_cliente_id;
            $vehiculo->nombre_vehiculo = $request->nombre_vehiculo;
            $vehiculo->modelo = $request->modelo;
            $vehiculo->placa = $request->placa;
            $vehiculo->estado = 1;
            $vehiculo->save();

            DB::commit();
            return response()->json([
                'estado' => true,
                'mensaje' => 'Vehículo guardado correctamente'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public function ajaxListadoVehiculos(Request $request)
    {
        $vehiculos = ClienteVehiculo::where('usuario_cliente_id', $request->cliente_id)->get();
        $listado = view('cliente.ajaxListadoVehiculos', compact('vehiculos'))->render();
        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }

    public function eliminarVehiculo(Request $request)
    {
        DB::beginTransaction();
        try {
            $vehiculo = ClienteVehiculo::find($request->vehiculo_id);
            $vehiculo->delete();
            DB::commit();
            return response()->json([
                'estado' => true,
                'mensaje' => 'Vehículo eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}

