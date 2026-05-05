<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use App\Utils\Respuesta;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function listado()
    {
        return view('cliente.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            //sacamos el listado
            //$rolCliente = env('ROL_CLIENTE');
            $rolCliente = 3;

            $clientes = User::where('rol_id', 3)->get();
            $valores = [
                'listado' => view('cliente.ajaxListado')->with(compact('clientes'))->render()
            ];
            $data = Respuesta::success($valores, "Datos Obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");

        }
        return $data;
    }

    public function guardarCliente(Request $request)
    {


        if ($request->ajax()) {

            // dd($request->all());
            //$rolCliente = env('ROL_CLIENTE');
            $rolCliente = 3;

            $cliente_id = $request->input('id');
            $nombre = $request->input('nombre');
            $ap_paterno = $request->input('ap_paterno');
            $ap_materno = $request->input('ap_materno');
            $cedula = $request->input('cedula');
            $celular = $request->input('celular');
            $nit = $request->input('nit');
            $razon_social = $request->input('razon_social');
            $direccion = $request->input('direccion');
            $nombre_referencia_1 = $request->input('nombre_referencia_1');
            $celular_referencia_1 = $request->input('celular_referencia_1');
            $nombre_referencia_2 = $request->input('nombre_referencia_2');
            $celular_referencia_2 = $request->input('celular_referencia_2');
            $nombre_referencia_3 = $request->input('nombre_referencia_3');
            $celular_referencia_3 = $request->input('celular_referencia_3');
            $email = $request->input('email');
            $password = $request->input('password');


            $usuario = Auth::user();

            if ($cliente_id == '0') {
                $cliente = new User();
                $cliente->usuario_creador_id = $usuario->id;

                $request->validate([
                    'nombre' => 'required',
                    'ap_paterno' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                ]);

            } else {

                $request->validate([
                    'nombre' => 'required',
                    'ap_paterno' => 'required',
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($cliente_id),
                    ],
                ]);

                $cliente = User::find($cliente_id);
                $cliente->usuario_modificador_id = $usuario->id;
            }

            $imagen = null;
            $imagen_CI_anverso = null;
            $imagen_CI_reverso = null;

            // IMAGEN
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');

                if ($file && $file->isValid()) {
                    $imagen = time() . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('imagenesClientes', $imagen, 'public');
                }
            }

            // CI ANVERSO
            if ($request->hasFile('imagen_CI_anverso')) {
                $file = $request->file('imagen_CI_anverso');

                if ($file && $file->isValid()) {
                    $imagen_CI_anverso = time() . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('imagenesClientes', $imagen_CI_anverso, 'public');
                }
            }

            // CI REVERSO
            if ($request->hasFile('imagen_CI_reverso')) {
                $file = $request->file('imagen_CI_reverso');

                if ($file && $file->isValid()) {
                    $imagen_CI_reverso = time() . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('imagenesClientes', $imagen_CI_reverso, 'public');
                }
            }

            $cliente->nombres = $nombre;
            $cliente->ap_paterno = $ap_paterno;
            $cliente->ap_materno = $ap_materno;
            $cliente->cedula = $cedula;
            $cliente->celular = $celular;
            $cliente->nit = $nit;
            $cliente->razon_social = $razon_social;
            $cliente->direccion = $direccion;
            $cliente->imagen = $imagen;
            $cliente->imagen_CI_anverso = $imagen_CI_anverso;
            $cliente->imagen_CI_reverso = $imagen_CI_reverso;
            $cliente->nombre_referencia_1 = $nombre_referencia_1;
            $cliente->celular_referencia_1 = $celular_referencia_1;
            $cliente->nombre_referencia_2 = $nombre_referencia_2;
            $cliente->celular_referencia_2 = $celular_referencia_2;
            $cliente->nombre_referencia_3 = $nombre_referencia_3;
            $cliente->celular_referencia_3 = $celular_referencia_3;
            $cliente->name = $nombre . " " . $ap_paterno . " " . $ap_materno;
            $cliente->email = $email;
            if ($cliente_id == '0') {
                $cliente->password = Hash::make($password);
            } else {
                if ($password != null && $password != "") {
                    $cliente->password = Hash::make($password);
                }
            }
            $cliente->rol_id = $rolCliente;
            $cliente->save();

            $data = Respuesta::success(null, "Datos Obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;
    }

    public function eliminarCliente(Request $request, Cliente $clientes)
    {

        if ($request->ajax()) {

            //INICIALIZAMOS LAS VARIABLES
            $cliente_id = $request->input('cliente');
            $usuario = Auth::user();

            if ($clientes->imagen && $clientes->imagen_CI_anverso && $clientes->imagen_CI_reverso) {
                Storage::disk('public')->delete($clientes->imagen);
                Storage::disk('public')->delete($clientes->imagen_CI_anverso);
                Storage::disk('public')->delete($clientes->imagen_CI_reverso);
            } else {
                $data = Respuesta::error(null, "Error al obtener los datos");
            }

            //BUSCAMOS AL CLIENTE
            $cliente = Cliente::find($cliente_id);
            $cliente->usuario_eliminador_id = $usuario->id;
            $cliente->save();

            //AHORA ELIMINAMOS
            Cliente::destroy($cliente_id);

            $data = Respuesta::success(null, "Se elimino con exito");

        } else {

            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
