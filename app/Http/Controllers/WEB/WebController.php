<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Jobs\SendActivationURL;
use App\Models\Huesped;
use App\Models\Personal;
use App\Models\User;
use App\Rules\UniqueEmailForUserableType;
use Faker\Provider\ar_EG\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

class WebController extends Controller
{

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:8'
            ]);

            if ($validator->fails()) {
                return redirect()->route('login')->withErrors(['error' => 'Credenciales requeridas.']);
            }

            $maxAttempts = 3;
            $decayMinutes = 1;

            //si se intenta logear muchas veces, se bloquea el login por un tiempo
            if (RateLimiter::tooManyAttempts('login_attempts', $maxAttempts)) {
                return redirect()->route('login')->withErrors(['error' => 'Demasiados intentos. Por favor, intente de nuevo.' ]);
            }
            //busca el usuario en la base de datos mediante el email
            $user = User::where('email', $request->email)->where('userable_type', 1)
            ->first();
            // si el role no es 1 ,no tiene permisos para acceder
            if (!$user->userable_type == 1) {
                return redirect()->route('login')->withErrors(['error' => 'No tiene permisos para acceder.']);
            }
            $user = Personal::where('email', $request->email)->first();
            // si el usuario no es admin, no tiene permisos para acceder
            if (!$user->rolID == 1) {
                return redirect()->route('login')->withErrors(['error' => 'No tiene permisos para acceder.']);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                RateLimiter::hit('login_attempts', $decayMinutes * 60);
                return redirect()->route('login')->withErrors(['error' => 'Credenciales incorrectas.']);
            }
            //si el usuario y la contraseña son correctos, se manda una ruta temporal para confirmar el correo

            RateLimiter::clear('login_attempts');
            $url = URL::temporarySignedRoute('confirm',now()->addMinute(5),['id' => $user->id]);
            // Generar el código de verificación
            
            $user->admin_code = rand(1000, 9999);
            
            $admin_code = $user->admin_code;
            // Encriptar el código de verificación usando Hash::make()
           
            $hashed_admin_code = Hash::make($admin_code);
            // Guardar el código encriptado en el usuario u otra ubicación si es necesario
            $user->admin_code = $hashed_admin_code;
            $user->save();
            // Despachar el trabajo para enviar el correo electrónico
            SendActivationURL::dispatch($url, $user, $admin_code);

            return redirect()->route('welcome')->with('message', 'Por favor, revise su correo para confirmar su cuenta.');

        } catch (\PDOException $e) {
            Log::error('PDOException during login: ' . $e->getMessage());
            return view('error', ['message' => 'Database error: ' . $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during login: ' . $e->getMessage());
            return view('error', ['message' => 'Database query error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Exception during login: ' . $e->getMessage());
            return view('error', ['message' => 'Unexpected error: ' . $e->getMessage()]);
        }
        
    }


    public function register(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    "rolID" => "required_if:userable_type,1",
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required",
                    "email" => ["required", "email", new UniqueEmailForUserableType($request->userable_type)],
                    "password" => "required",
                ]
            );

            if ($validation->fails()) {
                return response()->json(['error' => $validation->errors()], 400);
            }

            if ($request-> userable_type == 1) {
                $user = Personal::create([
                    "nombre" => $request->nombre,
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
