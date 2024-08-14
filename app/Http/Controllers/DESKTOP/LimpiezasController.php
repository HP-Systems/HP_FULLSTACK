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

    function asignarLimpiezas(Request $request){
        try{
            $validation = Validator::make($request->all(), [
                'personalID' => 'required|integer|exists:personal,id',
                'tarjeta' => 'required',
                'habitacion_reservaIDs' => 'required|array',
                'habitacion_reservaIDs.*' => 'integer|exists:habitaciones_reservas,id',
            ]);

            if ($validation->fails()) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error en las validaciones.',
                        'error' => $validation->errors()
                    ], 400
                );
            }

            $numero = $request->tarjeta;

            $tarjeta = DB::table('tarjetas as t')
                ->join('tipo_tarjeta as tt', 'tt.id', '=', 't.tipoID')
                ->select('t.id', 't.numero', 't.status', 'tt.tipo')
                ->where('t.numero', $numero)
                ->first();

            //validamos si se encuentra la tarjeta en la bd
            if (!$tarjeta) {
                return response()->json(
                    [
                        'status' => 404,
                        'data' => [],
                        'msg' => 'Tarjeta no encontrada.',
                        'error' => 'La tarjeta no se encuentra registrada'
                    ], 400
                );
            }

            $status = $tarjeta->status;
            $tipo = $tarjeta->tipo;

            //validamos si esta activa o no la tarjeta
            if($status == 0){
                return response()->json(
                    [
                        'status' => 404,
                        'data' => [],
                        'msg' => 'Tarjeta no encontrada.',
                        'error' => 'La tarjeta se encuentra deshabilitada y no puede ser utilizada.'
                    ], 400
                );
            }

            //validamos que solo sea del tipo limpieza
            if($tipo != 'Limpieza'){
                return response()->json(
                    [
                        'status' => 404,
                        'data' => [],
                        'msg' => 'Tarjeta no encontrada.',
                        'error' => 'Esta tarjeta no está permitida. Solo se aceptan tarjetas de tipo Limpieza.'
                    ], 400
                );
            }

            $tarjetaID = $tarjeta->id;
            $personalID = $request->personalID;
            $habitacion_reservaIDs = $request->habitacion_reservaIDs;
            $hoy = Carbon::now('America/Monterrey')->toDateString();

            foreach ($habitacion_reservaIDs as $habitacion_reservaID) {
                DB::table('limpiezas')->insert([
                    'personalID' => $personalID,
                    'tarjetaID' => $tarjetaID,
                    'habitacion_reservaID' => $habitacion_reservaID,
                    'fecha' => $hoy,
                    'status' => 1, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        
            return response()->json([
                'status' => 200,
                'data' => [],
                'msg' => 'Limpiezas asignadas con éxito.'
            ]);        
        } catch (\Exception $e) {
            Log::error('Exception during asignarLimpiezas: ' . $e->getMessage());
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
