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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
                'password' => 'required|string|min:8'
            ]);

            if ($validator->fails()) {
                // return response()->json([
                //     'message' => 'Credenciales requeridas.',
                // ], 401);
                return redirect()->route('login')->withErrors(['error' => 'Credenciales requeridas.']);
            }
            //busca el usuario en la base de datos mediante el email
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                // return response()->json([
                //     'message' => 'Credenciales incorrectas.',
                // ], 401);
                return redirect()->route('login')->withErrors(['error' => 'Credenciales incorrectas.']);
            }
            // si el role no es 1 ,no tiene permisos para acceder
            if ($user->userable_type == 2) {
                // return response()->json([
                //     'message' => 'No tiene permisos para acceder.',
                // ], 401);
                return redirect()->route('login')->withErrors(['error' => 'No tiene permisos para acceder.']);
            }
            $personal = Personal::where('id',$user->id)->first();
            // si el usuario no es admin, no tiene permisos para acceder
            if ($personal->rolID == 2 || $personal->rolID == 3) {
                // return response()->json([
                //     'message' => 'No tiene permisos para acceder..',
                // ], 401);
                return redirect()->route('login')->withErrors(['error' => 'No tiene permisos para acceder.']);
            }

            //si el usuario y la contraseña son correctos, se manda una ruta temporal para confirmar el correo

            $url = URL::temporarySignedRoute('confirm',now()->addMinute(5),['id' => $user->id]);
            // Generar el código de verificación
            
            $user->codigo = rand(1000, 9999);
            
            $admin_code = $user->codigo;
            // Encriptar el código de verificación usando Hash::make()
           
            $hashed_admin_code = Hash::make($admin_code);
            // Guardar el código encriptado en el usuario u otra ubicación si es necesario
            $user->codigo = $hashed_admin_code;
            $user->save();
            // Enviar al el correo electrónico
            SendActivationURL::dispatch($url, $user, $admin_code);

            // return response()->json([
            //     'message' => 'Por favor, revise su correo para confirmar su cuenta.',
            // ], 200);
            return redirect()->route('login')->with('success', 'Por favor, revise su correo para confirmar su cuenta.');

        } catch (\PDOException $e) {
            Log::error('PDOException during login: ' . $e->getMessage());
            //return view('error', ['message' => 'Database error: ' . $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException during login: ' . $e->getMessage());
            //return view('error', ['message' => 'Database query error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Exception during login: ' . $e->getMessage());
           // return view('error', ['message' => 'Unexpected error: ' . $e->getMessage()]);
        }
        
    }

    public function confirmEmail(Request $request, $id)
    {
        try {
            //si la ruta no está firmada, redirige al formulario de login con un error
            if (!$request->hasValidSignature()) {
                return redirect()->route('login.form')->withErrors(['error' => 'Invalid']);
            }
            //busca el usuario en la base de datos mediante el id
            $user = User::find($id);
            //si no encuentra el usuario, redirige al formulario de login con un error
            if (!$user) {
                return redirect()->route('login')->withErrors(['error' => 'User not found']);
            }
            //se verifica el usuario y se guarda en la base de datos
            //se direcciona a la vista de 2FA para la introducir el código
            $user->status = true;
            $user->save();

            //return view('2FA', ["id" => $id]);
            return response()->json([
                'message' => 'Usuario verificado con exito.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (QueryException $e) {
            Log::error('QueryException during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
        } catch (PDOException $e) {
            Log::error('PDOException during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
        }
        catch (ValidationException $e) {
            Log::error('ValidationException during email confirmation: ' . $e->getMessage());
            return redirect()->route('error');
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
