<?php

namespace App\Http\Controllers\GLOBAL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Reserva;


class ReservasController extends Controller
{
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
}
