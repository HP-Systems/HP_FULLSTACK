<?php

namespace App\Http\Controllers\GLOBAL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;

class HabitacionesController extends Controller
{
    public function habitacionesDisponibles(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "fecha_entrada" => "required|date",
                    "fecha_salida" => "required|date",
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
    
            $fechaEntrada = $request->fecha_entrada;
            $fechaSalida = $request->fecha_salida;
    
            // Reservas que se encuentran en las fechas dadas
            $reservasFecha = DB::table('reservas')
                ->where(function ($query) use ($fechaEntrada, $fechaSalida) {
                    $query->whereBetween('fecha_entrada', [$fechaEntrada, $fechaSalida])
                        ->orWhereBetween('fecha_salida', [$fechaEntrada, $fechaSalida]);
                })
                ->where('status', 1)
                ->pluck('id');
    
            // Habitaciones reservadas en esas fechas
            $habitacionesOcupadas = DB::table('habitaciones_reservas')
                ->whereIn('reservaID', $reservasFecha)
                ->pluck('habitacionID');
    
            // Tipos de habitaciones junto con las habitaciones disponibles
            $habitacionesDisponiblesPorTipo = DB::table('tipo_habitacion as th')
                ->leftJoin('habitaciones as h', 'th.id', '=', 'h.tipoID')
                ->whereNotIn('h.id', $habitacionesOcupadas)
                ->select(
                    'th.id as tipoID', 
                    'th.descripcion',
                    'th.capacidad',
                    'th.precio_noche',
                    'th.tipo', 
                    'h.id as habitacionID', 
                    'h.numero',
                    'h.imagen'
                )
                ->get()
                ->groupBy('tipoID');
    
            $result = $habitacionesDisponiblesPorTipo->map(function ($items, $key) {
                return [
                    'tipoID' => $items->first()->tipoID,
                    'tipo' => $items->first()->tipo,
                    'descripcion' => $items->first()->descripcion,
                    'capacidad' => $items->first()->capacidad,
                    'precio_noche' => $items->first()->precio_noche,
                    'habitaciones' => $items->map(function ($item) {
                        return [
                            'id' => $item->habitacionID,
                            'numero' => $item->numero,
                            'imagen' => $item->imagen 
                        ];
                    })->values()
                ];
            })->values();
    
            return response()->json(
                [
                    'status' => 200,
                    'data' => $result,
                    'msg' => 'Habitaciones obtenidas con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during habitacionesDisponibles: ' . $e->getMessage());
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
