<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use App\Models\Huesped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
