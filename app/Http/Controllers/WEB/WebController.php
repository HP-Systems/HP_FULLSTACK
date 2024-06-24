<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Jobs\SendActivationURL;
use App\Models\Huesped;
use App\Models\Personal;
use App\Models\User;
use App\Rules\UniqueEmailForUserableType;
use Faker\Provider\ar_EG\Person;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PDOException;

class WebController extends Controller
{
    public function loginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()->route('login')->withErrors(['error' => 'Credenciales incorrectas.']);
            }

            $maxAttempts = 3;
            $decayMinutes = 1;

            //si se intenta logear muchas veces, se bloquea el login por un tiempo
            if (RateLimiter::tooManyAttempts('login_attempts', $maxAttempts)) {
                return redirect()->back()->withErrors(['error' => 'Demasiados intentos. Por favor, intente de nuevo.' ]);
            }

            //busca el usuario en la base de datos mediante el email
            $user = User::where('email', $request->email)->where('userable_type', 1)
            ->first();

            
            // si el role no es 1 ,no tiene permisos para acceder
            if (!$user->userable_type == 1) {
                return redirect()->back()->withErrors(['error' => 'No tiene permisos para acceder.']);
            }

            $personal = Personal::where('id', $user->id)->first();

            // si el usuario no es admin, no tiene permisos para acceder
            if (!$personal->rolID == 1) {
                return redirect()->back()->withErrors(['error' => 'No tiene permisos para acceder.']);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                RateLimiter::hit('login_attempts', $decayMinutes * 60);
                return redirect()->back()->withErrors(['error' => 'Credenciales incorrectas.']);
            }

            //si el usuario y la contraseña son correctos, se manda una ruta temporal para confirmar el correo
            RateLimiter::clear('login_attempts');
            $url = URL::temporarySignedRoute('verify', now()->addMinute(5),['id' => $user->id]);
            session(['userId' => optional($user)->id]);
            
            // Generar el código de verificación
            $user->codigo = Str::upper(Str::random(6));
            $admin_code = $user->codigo;

            Log::info('Código de verificación: ' . $admin_code);


            // Encriptar el código de verificación usando Hash::make()
            $hashed_admin_code = Hash::make($admin_code);

            // Guardar el código encriptado en el usuario u otra ubicación si es necesario
            $user->codigo = $hashed_admin_code;
            $user->codigo = $hashed_admin_code;
            $user->save();
            // Enviar al el correo electrónico
            SendActivationURL::dispatch($url, $user, $admin_code);

            return redirect($url)->with('message', 'Por favor, revise su correo para confirmar su cuenta.');

        } catch (\PDOException $e) {
            Log::error('PDOException during login: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Error de servidor.']);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during login: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Error de servidor.']);
        } catch (\Exception $e) {
            Log::error('Exception during login: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Error de servidor.']);
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
                    "userable_type" => "required",
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


    public function verifyNumber(Request $request){
        try{
            $validation = Validator::make($request->all(), [
                'verification_code' => 'required|size:6'
            ]);

            if ($validation->fails()) {
                // Agrega un mensaje de error al validador solo si hay un error de código
                if ($validation->errors()->has('verification_code')) {
                    $validation->errors()->add('error', 'Código incorrecto. Inténtalo de nuevo.');
                }
                return redirect()->back()->withInput()->withErrors($validation);
            }

            //obtenermos el id de la session
            $userId = session('userId');
            $user = User::where('id', $userId)->first();
            Log::info('User ID: ' . $userId);

            $maxAttempts = 3;
            $decayMinutes = 1;

            //si intenta poner codigo varias veces, se bloquea por un tiempo
            if (RateLimiter::tooManyAttempts('login_attempts', $maxAttempts)) {
                return redirect()->route('login')->withErrors(['error' => 'Demasiados intentos. Por favor, intente de nuevo más tarde.' ]);
            }

            Log::info('Código: ' . $request->verification_code);
            $codigoVerificado = Hash::check($request->verification_code, $user->codigo);
            

            //si no coincide el codigo
            if (!$user || !$codigoVerificado) {
                RateLimiter::hit('login_attempts', $decayMinutes * 30);
                return redirect()->back()->withInput()->withErrors(['error' => 'Código incorrecto.']);
            }

            RateLimiter::clear('login_attempts');
            Auth::login($user);
            $token = $user->createToken("token")->plainTextToken;
            $user->save();

            return redirect()->route('home')->with(['token' => $token]);     
        } catch (\Exception $e) {
            Log::error('Error en el inicio de sesión: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Error de servidor.']);
        }
    }

    public function logout(Request $request){
        try {
            $user = $request->user();
            Log::info("El usuario con ID {$user->id} ha cerrado sesión exitosamente.");

            $request->user()->tokens()->delete();

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login');
        } catch (\Exception $e) {
            // Manejo de excepciones
            Log::error('Error en el cierre de sesión: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Error de servidor']);
        }
    }
}
