<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon; 

class ReservasController extends Controller
{
    public function traerReservas(){
        try{
            $hoy = Carbon::now();
            $haceUnaSemana = $hoy->copy()->subWeek();

            // Consulta para obtener las reservas a partir de una semana antes de hoy en adelante
            $reservas = DB::table('reservas')
                ->where('fecha_entrada', '>=', $haceUnaSemana)
                ->get();

            // IDs de las reservas
            $reservaIDs = $reservas->pluck('id');

            //Habitaciones asociadas a esas reservas
            $habitacionesReservadas = DB::table('habitaciones_reservas')
                ->whereIn('reservaID', $reservaIDs)
                ->get();

            // IDs de las habitaciones
            $habitacionIDs = $habitacionesReservadas->pluck('habitacionID');

            // Detalles de las habitaciones incluyendo el tipo de habitación
            $habitacionesDetalles = DB::table('habitaciones')
                ->whereIn('id', $habitacionIDs)
                ->get();

            // IDs de los tipos de habitaciones
            $tipoHabitacionIDs = $habitacionesDetalles->pluck('tipoID');

            // Detalles de los tipos de habitaciones
            $tiposHabitacion = DB::table('tipo_habitacion')
                ->whereIn('id', $tipoHabitacionIDs)
                ->get();

            // Combinar detalles del tipo de habitación con las habitaciones
            $habitacionesConDetalles = $habitacionesDetalles->map(
                function($habitacion) use ($tiposHabitacion) {
                $habitacion->tipo_habitacion = $tiposHabitacion->where('id', $habitacion->tipoID)->first();
                return $habitacion;
            });

            // Combinar reservas con habitaciones reservadas y sus detalles
            $reservasConHabitaciones = $reservas->map(
                function($reserva) use ($habitacionesReservadas, $habitacionesConDetalles) {
                $habitaciones = $habitacionesReservadas->where('reservaID', $reserva->id)->map(
                    function($habitacionReserva) use ($habitacionesConDetalles) {
                    return $habitacionesConDetalles->where('id', $habitacionReserva->habitacionID)->first();
                });
                $reserva->habitaciones = $habitaciones;
                return $reserva;
            });


            return response()->json(
                [
                    'status' => 200,
                    'data' => $reservas,
                    'msg' => 'Reservas obtenidas con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during habitacionesDisponibles: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error al traer las reservas',
                    'error' => $e->getMessage()
                ], 500
            );
        }
    }
}
