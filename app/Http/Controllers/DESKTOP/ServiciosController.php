<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class ServiciosController extends Controller
{    
    public static function serviciosSolicitados(){
        try{
            $resultados = DB::table('servicios_reservas as sr')
                ->join('servicios as s', 's.id', '=', 'sr.servicioID')
                ->join('tipo_servicio as ts', 'ts.id', '=', 's.tipoID')
                ->join('habitaciones_reservas as hr', 'hr.id', '=', 'sr.habitacionReservaID')
                ->join('reservas as r', 'r.id', '=', 'hr.reservaID')
                ->join('habitaciones as h', 'h.id', '=', 'hr.habitacionID')
                ->select(
                    'sr.id', 
                    'hr.reservaID', 
                    'sr.fecha', 
                    'sr.status', 
                    'sr.cantidad', 
                    's.nombre', 
                    's.descripcion', 
                    's.precio', 
                    'ts.tipo', 
                    'hr.habitacionID', 
                    'h.numero'
                )
                ->whereDate('sr.fecha', '=', DB::raw('CURDATE()'))
                ->where('sr.status', '=', 1)
                ->where('r.status', '=', 1)
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $resultados,
                'msg' => 'Servicios solicitados obtenidos correctamente',
                'error' => []
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during service retrieval: ' . $e->getMessage());
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
