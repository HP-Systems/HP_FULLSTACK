<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 
use App\Models\ServicioReserva;

class ServiciosController extends Controller
{    
    public function serviciosSolicitados(){
        try{
            $fechaHoy = Carbon::now('America/Monterrey')->format('Y-m-d');

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
                ->whereDate('sr.fecha', '=', $fechaHoy)
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
            Log::error('Exception during servicios solicitados: ' . $e->getMessage());
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

    public function completarServicio($id){
        try{
            $servicio = ServicioReserva::findOrFail($id);
            $servicio->status = 2;
            
            if($servicio->save()){
                return response()->json([
                    'status' => 200,
                    'data' => [],
                    'msg' => 'Servicio completado correctamente',
                    'error' => []
                ], 200);
            } else{
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'Error al completar el servicio',
                    'error' => []
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception during servicios solicitados: ' . $e->getMessage());
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
