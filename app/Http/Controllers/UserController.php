<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function listado()
    {

        $roles = Rol::all();

        $sucursales = Sucursal::all();

        return view('user.listado')->with(compact('roles', 'sucursales'));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            //sacamos el listado
            //$usuarios = User::all();
            $usuarios = User::where('rol_id', '!=', 5)->get();
            $valores = [
                'listado' => view('user.ajaxListado')->with(compact('usuarios'))->render()
            ];
            $data = Respuesta::success($valores, "Datos Obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");

        }
        return $data;
    }

    public function guardarUser(Request $request)
    {
        if ($request->ajax()) {
            $user_id = $request->input('id');
            $sucursal_id = $request->input('sucursal_id');
            $rol_id = $request->input('rol_id');
            $nombre = $request->input('nombre');
            $ap_paterno = $request->input('ap_paterno');
            $ap_materno = $request->input('ap_materno');
            $cedula = $request->input('cedula');
            $celular = $request->input('celular');
            $name = $request->input('name');
            $email = $request->input('email');
            $password = Hash::make($request->input('password'));
            $usuario = Auth::user();

            if ($user_id == '0') {
                $user = new User();
                $user->usuario_creador_id = $usuario->id;
            } else {
                $user = User::find($user_id);
                $user->usuario_modificador_id = $usuario->id;
            }

            $user->rol_id = $rol_id;
            $user->sucursal_id = $sucursal_id;
            $user->nombres = $nombre;
            $user->ap_paterno = $ap_paterno;
            $user->ap_materno = $ap_materno;
            $user->cedula = $cedula;
            $user->celular = $celular;
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->save();

            $data = Respuesta::success(null, "Datos Obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;
    }

    public function eliminarUser(Request $request)
    {

        if ($request->ajax()) {

            //INICIALIZAMOS LAS VARIABLES
            $user_id = $request->input('user');
            $usuario = Auth::user();

            //BUSCAMOS AL USER
            $user = User::find($user_id);
            $user->usuario_eliminador_id = $usuario->id;
            $user->save();

            //AHORA ELIMINAMOS
            User::destroy($user_id);

            $data = Respuesta::success(null, "Se elimino con exito");

        } else {

            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
    public function getUser($id)
    {
        return response()->json(
            User::select('id', 'pago_diario', 'horas_base')->find($id)
        );
    }

}
