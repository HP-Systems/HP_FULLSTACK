<?php

namespace App\Http\Controllers\MOVIL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservasController extends Controller
{
    public function createReserva(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "fecha_entrada" => "required",
                    "fecha_salida" => "required",
                    "huesped" => "required",
                    "habitaciones" => "required",
                ]
            );

            if ($validation->fails()) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Todos los campos son necesarios y deben cumplir con el formato adecuado.',
                        'error' => $validation->errors()
                    ], 400
                );
            }

            return response()->json(
                [
                    'status' => 200,
                    'data' => ["Data de la reserva"],
                    'msg' => 'Reserva creada con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error de servidor.',
                    'error' => $e->getMessage(),
                ], 500
            );
        }
    }
}
