<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class ServiciosController extends Controller
{    
    public function obtenerServiciosSolicitados($fecha1 = null, $fecha2 = null){
        try{
            $fechaActual = Carbon::today()->toDateString();

            if (is_null($fecha1) && is_null($fecha2)) {
                $fecha1 = $fechaActual;
                $fecha2 = $fechaActual;
            }

            $serviciosSolicitados = DB::table('servicios_reservas as sr')
            ->join('servicios as s', 's.id', '=', 'sr.servicioID')
            ->join('tipo_servicio as ts', 'ts.id', '=', 's.tipoID')
            ->join('habitaciones_reservas as hr', 'hr.id', '=', 'sr.habitacionReservaID')
            ->join('habitaciones as h', 'h.id', '=', 'hr.habitacionID')
            ->join('tipo_habitacion as th', 'th.id', '=', 'h.tipoID')
            ->join('reservas as r', 'r.id', '=', 'hr.reservaID')
            ->select(
                'sr.id as idservicio_reserva',
                's.nombre',
                's.descripcion',
                's.precio',
                'ts.tipo as tipo_servicio',
                'sr.fecha',
                'sr.cantidad',
                'hr.habitacionID',
                'h.numero',
                'th.tipo as tipo_habitacion',
                'r.fecha_entrada',
                'r.fecha_salida',
                'r.status'
            )
            ->where('sr.status', '!=', 0)
            ->where('r.status', 1)
            ->whereBetween('sr.fecha', [$fecha1, $fecha2])
            ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $serviciosSolicitados,
                    'msg' => 'Servicios solicitados obtenidos correctamente',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during obtenerServiciosSolicitados: ' . $e->getMessage());
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
