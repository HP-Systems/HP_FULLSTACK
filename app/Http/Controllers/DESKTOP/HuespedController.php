<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use App\Models\Huesped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class HuespedController extends Controller
{
    public function huespedes()
    {
        try {
            // Obtener todos los huespedes con sus emails por medio de una consulta SQL
            $huesped = Huesped::select('huespedes.*', 'users.email')
                ->leftJoin('users', 'users.userable_id', '=', 'huespedes.id')
                ->where('users.userable_type', 2)
                ->get();
            
            if ($huesped->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron huespedes..',
                ], 404);
            }

            return response()->json(
                [
                    'data'=>$huesped,
                    'message' => 'Huespedes obtenidos con éxito',
                    'status' => 200,
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al obtener los huespedes..',
                $e->getMessage(),
            ], 500);
            Log::error($e->getMessage());
        }
        catch (\PDOException $e) {
            return response()->json([
                'message' => 'Hubo um problema al obtener los huespedes',
            ], 500);
            //el error se puede ver en el log de laravel
            Log::error($e->getMessage());
        }
        catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Hubo um problema al obtener los huespedes.',
        
            ], 500);
            //el error se puede ver en el log de laravel
            Log::error($e->getMessage());
        }
    }
    public function editar( Request $request, $id)
    {
        try{
            // obtener el huesped por medio de su id
            $huesped = Huesped::find($id);
            if($huesped == null)
            {
                return response()->json([
                    'message' => 'No se encontró el huesped favor de enviar el id del huesped',
                ], 404);
            }
          
            $validation = Validator::make(
                $request->all(),
                [
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required|digits:10",
                ]
            );
            if ($validation->fails()) {
                return response()->json(['error' => $validation->errors()], 400);
            }
            $huesped->nombre = $request->nombre;
            $huesped->apellido = $request->apellido;
            $huesped->telefono = $request->telefono;
            $huesped->save();
            return response()->json([
                'data'=>$huesped,
                'message' => 'Huesped actualizado con éxito',
                'status' => 200,
            ]);

        }catch(\Exception $e)
        {
            return response()->json([
                'message' => 'Hubo un error al actualizar el huesped.',
                $e->getMessage(),
            ], 500);
            Log::error($e->getMessage());
        }
        catch (\PDOException $e) {
            return response()->json([
                'message' => 'Hubo um problema al actualizar el huesped',
        
            ], 500);
            //el error se puede ver en el log de laravel
            Log::error($e->getMessage());
        }
        catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Hubo um problema al actualizar el huesped.',
        
            ], 500);
            //el error se puede ver en el log de laravel
            Log::error($e->getMessage());
        }
        
    }

    /*public function editarContra(Request $request){
        $validation = Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'password' => 'required',
            ]
        );

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 400);
        }

        $user = User::where('id', $request->id)->first();
        $user->password = Hash::make($request->password);
        
        $user->save();
        return response()->json([
            'data' => $user,
            'message' => 'Contraseña actualizada con éxito',
            'status' => 200,
        ]);
        
    }*/

}
