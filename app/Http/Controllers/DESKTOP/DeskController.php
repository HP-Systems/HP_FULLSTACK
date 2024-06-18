<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use App\Models\Huesped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\UniqueEmailForUserableType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Personal;
use Illuminate\Support\Facades\Log;

class DeskController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    "email" => "required|email",
                    "password" => "required",
                ]
            );

            if ($validation->fails()) {
                return response()->json(['error' => $validation->errors()], 400);
            }

            $user = User::where('email', $request->email)->
            where('userable_type', 1)
            ->first();
            if (!$user) {
                return response()->json([
                    'message' => 'Credenciales incorrectas.',
                ], 400);
            }
            
            if (!$user->rolID == 1 || !$user->rolID == 2) {
                return response()->json([
                    'message' => 'No tiene permisos para acceder.',
                ], 400);
            }


            if (!$user || !Hash::check($request->password, $user->password)) {

                return response()->json([
                    'message' => 'Credenciales incorrectas.',
                ], 400);
            }
        
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario logueado con exito.',
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al loguear el usuario.'
            ], 500);
            Log::error($e->getMessage());
        }
        catch (\PDOException $e) {
            return response()->json([
                'message' => 'Hubo um problema al loguear el usuario',
        
            ], 500);
            //el error se puede ver en el log de laravel
            Log::error($e->getMessage());
        }
        
    }
    public function register(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required",
                    "rolID" => "required",
                    "email" => ["required", "email", new UniqueEmailForUserableType($request->userable_type)],
                    "password" => "required",
                ]
            );

            if ($validation->fails()) {
                return response()->json(['error' => $validation->errors()], 400);
            }

            if ($request-> userable_type == 1) {
                $user = Personal::create([
                    //"nombre" => $request->nombre,
                    "apellido" => $request->apellido,
                    "telefono" => $request->telefono,
                    "rolID" => $request->rolID,
                ]); 

                $user = User::create([
                    "email" => $request->email,
                    "password" => Hash::make($request->password),
                    "userable_id" => $user->id,
                    "userable_type" => $request->userable_type,
                ]);
                return response()->json([
                    'message' => 'Usuario creado con exito.',
                ], 200);
            }

            $user = Huesped::create([
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "telefono" => $request->telefono,
            ]);
           
            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "userable_id" => $user->id,
                "userable_type" => $request->userable_type,
            ]);

            return response()->json([
                'message' => 'Usuario creado con exito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear el usuario.'
                
            ], 500);
            Log::error($e->getMessage());
            
        }
        catch (\PDOException $e) {
            return response()->json([
                'message' => 'Hubo um problema al crear el usuario',
        
            ], 500);
            //el error se puede ver en el log de laravel
            Log::error($e->getMessage());
        }
        

    }
}
