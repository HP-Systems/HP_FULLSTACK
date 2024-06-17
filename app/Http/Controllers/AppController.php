<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\UniqueEmailForUserableType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Huesped;


class AppController extends Controller
{
    public function register(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required|max:10",
                    "email" => ["required", "email", new UniqueEmailForUserableType($request->userable_type)],
                    "password" => "required|min:8",

                    "email.email" => "El campo :attribute es incorrecto",
                    "password.min" => "La contraseña debe tener al menos :min caracteres",
                ]
            );

            if ($validation->fails()) {
                return response()->json(['error' => $validation->errors()], 400);
            }

            $huesped = Huesped::create([
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "telefono" => $request->telefono,
            ]);

            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "userable_id" => $huesped->id,
                "userable_type" => $request->userable_type,
            ]);


            return response()->json([
                'message' => 'Usuario creado con exito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear el usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request){
        try{
            $validate = Validator::make(
                $request->all(),
                [
                    "email" => "required | email",
                    "password" => "required",
                ]
            );

            if ($validate->fails()) {
                return response()->json(['error' => $validate->errors()], 400);
            }

            $user = User::where('email', $request->email)
                        ->where('userable_type', 2)
                        ->first();

            if (!is_null($user) && Hash::check($request->password, $user->password)) {
                if ($user->status == true) {
                    $token = $user->createToken('login')->plainTextToken;
                    $user->save();

                    return response()->json([
                        'message' => 'Login successful',
                        'user' => $user,
                        'token' => $token,
                    ], 201);
                                       
                } else {
                    return response()->json(['error' => 'Cuenta desactivada.'], 400);
                }
            } else{
                return response()->json(['error' => 'Los datos son incorrectos. Inténtalo de nuevo.'], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en el inicio de sesion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
