<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
use App\Rules\UniqueEmailNotId;
use App\Rules\UniqueEmailForUserableType;

class UsersController extends Controller
{
    public function index(){
        $personal = DB::table('personal as p')
            ->leftjoin('users as u', function ($join) {
                $join->on('u.userable_id', '=', 'p.id')
                     ->where('u.userable_type', '=', '1'); 
            })
            ->join('roles as r', 'r.id', '=', 'p.rolID')
            ->selectRaw('u.email, 
                u.status, 
                p.id, 
                p.nombre,
                p.apellido,
                CONCAT(p.nombre, " ", p.apellido) as nombre_completo, 
                p.telefono, 
                r.id as rolID,
                r.nombre as rol') 
            ->get();

        $roles = Rol::where('status', '=', 1)->get();
        $currentUserId = auth()->user()->id;

        return view('users.users', compact('personal', 'roles', 'currentUserId'));
    }

    public function insertPersonal(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required|digits:10|numeric",
                    "email" => ["required", "email", new UniqueEmailForUserableType($request->rol)],
                    'rol' => 'required|exists:roles,id'
                ],
                [
                    'telefono.digits' => 'El teléfono debe tener 10 dígitos.',
                    'telefono.numeric' => 'El teléfono debe ser un número.',
                    'email.email' => 'El email debe tener un formato válido.'
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
            Log::error('Exception during insertPersonal: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al crear el usuario.']);
        }
    }

    public function editPersonal(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required|digits:10|numeric",
                    "email" => [
                        "required", 
                        "email", 
                        new UniqueEmailNotId($request->rol, $request->id)
                    ],
                    'rol' => 'required|exists:roles,id'
                ],
                [
                    'telefono.digits' => 'El teléfono debe tener 10 dígitos.',
                ]
                
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $personal = Personal::find($request->id);
            $personal->nombre = $request->nombre;
            $personal->apellido = $request->apellido;
            $personal->telefono = $request->telefono;
            $personal->rolID = $request->rol;
            $personal->save();

            $user = User::where('userable_id', $request->id)->where('userable_type', 1)->first();
            $user->email = $request->email;
            $user->save();

            return response()->json(['edit' => 'Usuario actualizado con éxito'], 200);

        } catch (\Exception $e) {
            Log::error('Exception during editPersonal: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el usuario.']);
        }
    }

    public function cambiarStatus(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    'status' => 'required'
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $user = User::where('userable_id', $request->id)->where('userable_type', 1)->first();
            $user->status = $request->status;
            $user->save();

            return response()->json(['msg' => 'Usuario actualizado con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during cambiarStatus: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el usuario.']);
        }
    }

    public function indexTipos(){
        $roles = Rol::all();

        return view('users.tipos_personal', compact('roles'));
    }

    public function insertTipoPersonal(Request $request){
        try{
            $validation = Validator::make(
                $request->all(), 
                [
                    'tipoPersonal' => 'required',
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $rol = Rol::create([
                'nombre' => $request->tipoPersonal,
                'status' => 1
            ]);

            return response()->json(['msg' => 'El tipo de personal ha sido creado correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during insertTipoPersonal: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al crear el tipo de personal.']);
        }
    }

    public function editTipoPersonal(Request $request){
        try{
            $validation = Validator::make(
                $request->all(), 
                [
                    'tipoPersonal' => 'required',
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $tipo_personal = Rol::find($request->id);
            $tipo_personal->nombre = $request->tipoPersonal;
            $tipo_personal->save();

            return response()->json(['edit' => 'El tipo de personal ha sido editado correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during editTipoPersonal: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al editar el tipo de personal.']);
        }
    }
    
    public function cambiarStatusTipo(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    'status' => 'required'
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $tipo_personal = Rol::find($request->id);
            $tipo_personal->status = $request->status;
            
            if ($tipo_personal->save()) {
                $personales = Personal::where('rolID', $request->id)->get();

                foreach ($personales as $personal) {
                    User::where('userable_id', $personal->id)
                        ->where('userable_type', 1)
                        ->update(['status' => $request->status]);
                }
    
                return response()->json(['msg' => 'Tipo de personal y personal relacionado actualizados con éxito'], 200);
            } else {
                return response()->json(['error' => 'No se pudo actualizar el tipo de personal.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception during cambiarStatus: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el tipo de personal.']);
        }
    }
}


