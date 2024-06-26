<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\UniqueEmailForUserableType;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\Rol;
use App\Models\User;
use App\Models\Personal;
use App\Mail\passwPersonalMail;

class UsersController extends Controller
{
    public function index(){
        $personal = DB::table('personal as p')
            ->leftjoin('users as u', function ($join) {
                $join->on('u.userable_id', '=', 'p.id')
                     ->where('u.userable_type', '=', '1'); 
            })
            ->join('roles as r', 'r.id', '=', 'p.rolID')
            ->selectRaw('u.email, u.status, CONCAT(p.nombre, " ", p.apellido) as nombre_completo, p.telefono, r.nombre as rol')
            ->get();

        $roles = Rol::all();

        return view('users.users', compact('personal', 'roles'));
    }

    public function insertPersonal(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required|max:10",
                    "email" => ["required", "email", new UniqueEmailForUserableType($request->rol)],
                    'rol' => 'required|exists:roles,id'
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }


            $personal = Personal::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'telefono' => $request->telefono,
                'rolID' => $request->rol,
            ]);

            $password = Str::random(8);
    
            // Crear el usuario correspondiente
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($password), 
                'userable_id' => $personal->id,
                'userable_type' => 1, 
                'status' => 1
            ]);
            
            Mail::to($request->email)->send(new passwPersonalMail($user, $password));

            return response()->json(['msg' => 'Usuario creado con éxito'], 200);

        } catch (\Exception $e) {
            Log::error('Exception during login: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al crear el usuario.']);
        }

    }

}


