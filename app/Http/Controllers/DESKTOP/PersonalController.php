<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use App\Mail\passwPersonalMail;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;

class PersonalController extends Controller
{
    public function obtenerPersonal()
    {
        try {
            //traer a los usuarios de tipo personal con su rol
            $personal = User::where('userable_type', 1)->join('personal', 'users.userable_id', '=', 'personal.id')->join('roles', 'personal.rolID', '=', 'roles.id')->select('users.status', 'users.id as User_id', 'users.email', 'personal.id as Personal_id', 'personal.nombre', 'personal.apellido', 'personal.telefono', 'roles.nombre as rol')
                ->where('personal.rolID', '!=', 1) // Filtrar roles diferentes a 1
                ->get();


            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'Personal obtenido correctamente',
                    'data' => $personal
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al obtener el personal',
                    'data' => $e->getMessage()
                ]
            );
        } catch (\PDOException $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al obtener el personal',
                    'data' => $e->getMessage()
                ]
            );
        } catch (\Illuminate\Validation\Validator $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al obtener el personal',
                    'data' => $e->getMessage()
                ]
            );
        }
    }
    public function crearPersonal(Request $request)
    {

        try {
            $message = [
                'nombre.required' => 'El campo nombre es requerido',
                'apellido.required' => 'El campo apellido es requerido',
                'telefono.required' => 'El campo telefono es requerido',
                'telefono.digits' => 'El campo telefono debe tener 10 digitos',
                'telefono.numeric' => 'El campo telefono debe ser numerico',
                'email.required' => 'El campo email es requerido',
                'email.email' => 'El campo email debe ser un email valido',
                'rol.required' => 'El campo rol es requerido',
                'rol.exists' => 'El campo rol no existe'
            ];
            $vaidator = FacadesValidator::make($request->all(), [
                'nombre' => 'required',
                'apellido' => 'required',
                'telefono' => 'required|digits:10|numeric',
                'email' => 'required|email',
                'rol' => 'required|exists:roles,id'
            ], $message);
            if ($vaidator->fails()) {
                return response()->json(
                    [
                        'status' => 406,
                        'msg' => $vaidator->errors()->first(),
                        'data' => $vaidator->errors()
                    ]
                );
            }
            if (User::where('email', $request->email)->where('userable_type', 1)->exists()) {
                return response()->json(
                    [
                        'status' => 409,
                        'msg' => 'El usuario con este email ya existe.',
                    ]
                );
            }
            $personal = Personal::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'telefono' => $request->telefono,
                'rolID' => $request->rol
            ]);
            $password = Str::random(15);
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($password),
                'userable_id' => $personal->id,
                'userable_type' => 1
            ]);

            $userData = [
                'User_id' => $user->id,
                'Personal_id' => $personal->id,
                'email' => $request->email,
                'nombre' => $personal->nombre,
                'apellido' => $personal->apellido,
                'telefono' => $personal->telefono
            ];

            Mail::to($request->email)->send(new passwPersonalMail($user, $password));
            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'Personal creado correctamente',
                    'data' => $userData
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',

                ]
            );
        } catch (\PDOException $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',
                ]
            );
        } catch (\Illuminate\Validation\Validator $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',
                ]
            );
        }
    }
    public function editarPersonal(Request $request)
    {
        try {
            $message = [
                'nombre.required' => 'El campo nombre es requerido',
                'apellido.required' => 'El campo apellido es requerido',
                'telefono.required' => 'El campo telefono es requerido',
                'telefono.digits' => 'El campo telefono debe tener 10 digitos',
                'telefono.numeric' => 'El campo telefono debe ser numerico',
                'rol.required' => 'El campo rol es requerido',
                'rol.exists' => 'El campo rol no existe'
            ];
            $vaidator = FacadesValidator::make($request->all(), [
                'nombre' => 'required',
                'apellido' => 'required',
                'telefono' => 'required|digits:10|numeric',
                'rol' => 'required|exists:roles,id'
            ], $message);
            if ($vaidator->fails()) {
                return response()->json(
                    [
                        'status' => 406,
                        'msg' => $vaidator->errors()->first(),
                        'data' => $vaidator->errors()
                    ]
                );
            }
            $personal = Personal::find($request->id);
            if (!$personal) {
                return response()->json(
                    [
                        'status' => 404,
                        'msg' => 'Personal no encontrado',
                    ]
                );
            }
            if ($request->rol == 1) {
                return response()->json(
                    [
                        'status' => 406,
                        'msg' => 'No se puede asignar el rol de administrador a un personal',
                    ]
                );
            }
            $personal->nombre = $request->nombre;
            $personal->apellido = $request->apellido;
            $personal->telefono = $request->telefono;
            $personal->rolID = $request->rol;
            $personal->save();
            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'Personal editado correctamente',
                    'data' => $personal
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',

                ]
            );
        } catch (\PDOException $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',
                ]
            );
        } catch (\Illuminate\Validation\Validator $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',
                ]
            );
        }
    }
    public function desactivarPersonal(Request $request)
    {

        try {
            $personal = User::find($request->id);
            if (!$personal) {
                return response()->json(
                    [
                        'status' => 404,
                        'msg' => 'Personal no encontrado',
                    ]
                );
            }
            $personal->status = 0;
            $personal->save();
            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'Personal desactivado correctamente',
                    'data' => $personal
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',

                ]
            );
        } catch (\PDOException $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',
                ]
            );
        } catch (\Illuminate\Validation\Validator $e) {
            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error al crear el personal',
                ]
            );
        }
    }
}
