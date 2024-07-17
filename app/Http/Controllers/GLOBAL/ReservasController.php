<?php

namespace App\Http\Controllers\GLOBAL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Reserva;


class ReservasController extends Controller
{
    public function createReserva(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "fecha_entrada" => "required",
                    "fecha_salida" => "required",
                    "huespedID" => "required",
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

            $reserva = Reserva::create([
                'huespedID' => $request->huespedID,
                'fecha_entrada' => $request->fecha_entrada,
                'fecha_salida' => $request->fecha_salida,
                'status' => 1
            ]);

            $now = now();
            if (is_array($request->habitaciones) && count($request->habitaciones) > 0) {
                foreach ($request->habitaciones as $habitacion) {
                    if (isset($habitacion['id'])) {
                        DB::table('habitaciones_reservas')->insert([
                            'reservaID' => $reserva->id,
                            'habitacionID' => $habitacion['id'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }
                }
            } else {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'El campo habitaciones es requerido y debe ser un array no vacío.',
                        'error' => []
                    ], 400
                );
            }

            $reservaCreada = Reserva::with('habitaciones')->find($reserva->id);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $reservaCreada,
                    'msg' => 'Reserva creada con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during createReserva: ' . $e->getMessage());
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

    public function cancelarReserva($idReserva){
        try{
            if (!is_numeric($idReserva) || (int)$idReserva <= 0) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'El ID de la reserva debe ser un número entero positivo.',
                        'error' => []
                    ], 400
                );
            }

            $reserva = Reserva::find($idReserva);
            
            if (!$reserva) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Reserva no encontrada',
                        'error' => []
                    ], 400
                );
            }

            $reserva->status = 0;
            $reserva->save();
            
            return response()->json(
                [
                    'status' => 200,
                    'data' => [],
                    'msg' => 'Reserva cancelada con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {

            Log::error('Exception during cancelarReserva: ' . $e->getMessage());
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

    public function detalleReserva($idReserva){
        try{
            if (!is_numeric($idReserva) || (int)$idReserva <= 0) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'El ID proporcionado no es válido.',
                        'error' => ['El ID debe ser un número entero positivo.']
                    ], 400
                );
            }

            $reservas = Reserva::with('habitaciones')
                    ->where('id', $idReserva)
                    ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $reservas,
                    'msg' => 'Detalle de reserva obtenido con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during detalleReserva: ' . $e->getMessage());
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
