<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class LimpiezasController extends Controller
{
    function habitacionesLimpieza(){
        try{
            $hoy = Carbon::now('America/Monterrey')->toDateString();

            $habitaciones = DB::select("
                SELECT hr.habitacionID, th.tipo, h.numero, hr.id as habitacion_reservaID, 
                    hr.reservaID, CONCAT(p.nombre, ' ', p.apellido) as persona_limpieza, l.tarjetaID, l.fecha, l.status as status_limpieza,
                    CASE 
                        WHEN l.id IS NULL THEN 'Sin Asignar'
                        ELSE 'Asignada'
                    END as estado_limpieza
                FROM habitaciones h
                JOIN tipo_habitacion th ON th.id = h.tipoID
                JOIN habitaciones_reservas hr ON hr.habitacionID = h.id
                JOIN reservas r ON r.id = hr.reservaID AND r.status = 1
                LEFT JOIN limpiezas l ON l.habitacion_reservaID = hr.id AND l.fecha = ? AND l.status != 0
                LEFT JOIN personal p ON p.id = l.personalID
                WHERE ? BETWEEN r.fecha_entrada AND r.fecha_salida
            ", [$hoy, $hoy]);

            return response()->json([
                'status' => 200,
                'data' => $habitaciones,
                'msg' => 'Habitaciones obtenidas con éxito.'
            ]);

        } catch (\Exception $e) {
            Log::error('Exception during habitacionesSinLimpieza: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error de servidor',
                    'error' => $e->getMessage(),
                ], 500
            );
        }
    }
}
