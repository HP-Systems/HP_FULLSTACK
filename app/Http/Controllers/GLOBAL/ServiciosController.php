<?php

namespace App\Http\Controllers\GLOBAL;

use App\Events\AccessEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Servicio;
use App\Models\TipoServicio;

class ServiciosController extends Controller
{
    public function index(){
        try{
            //servicios con su tipo de servicio
            //$servicios = Servicio::with('tipoServicio')->get();
            
            //tipo de servicio con sus servicios
            $servicios = TipoServicio::with('servicios')->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $servicios,
                    'msg' => 'Servicios obtenidos con éxito.',
                    'error' => []
                ], 200
            );

        } catch (\Exception $e) {
            Log::error('Exception during index servicios: ' . $e->getMessage());
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
    
    public function insertarServiciosReserva(Request $request) {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    "reservaID" => "required|integer",
                    "servicios" => "required|array",
                    "servicios.*.servicioID" => "required|integer",
                    "servicios.*.habitacionID" => "required|integer",
                    "servicios.*.cantidad" => "required|integer|min:1"
                ]
            );
    
            if ($validation->fails()) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error de validación',
                        'error' => $validation->errors()
                    ], 400
                );
            }
    
            $reservaID = $request->reservaID;
            $servicios = $request->servicios;
    
            foreach ($servicios as $servicio) {
                // Obtener el ID de habitaciones_reservas
                $habitacionReserva = DB::table('habitaciones_reservas')
                    ->where('habitacionID', $servicio['habitacionID'])
                    ->where('reservaID', $reservaID)
                    ->first();

                
    
                if ($habitacionReserva) {
                    // Insertar en servicios_reservas
                    DB::table('servicios_reservas')->insert([
                        'servicioID' => $servicio['servicioID'],
                        'habitacionReservaID' => $habitacionReserva->id,
                        'cantidad' => $servicio['cantidad'],
                        'fecha' => now()->toDateString(), // Fecha en formato YYYY-mm-dd
                        'status' => 1, 
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    return response()->json(
                        [
                            'status' => 404,
                            'data' => [],
                            'msg' => 'No se encontró la habitación para la reserva especificada.',
                            'error' => []
                        ], 404
                    );
                }
            }
    
            AccessEvent::dispatch("Servicios insertados en la reserva");
            return response()->json(
                [
                    'status' => 200,
                    'data' =>[],
                    'msg' => 'Servicios insertados con éxito.',
                    'error' => []
                ], 200
            );
    
        } catch (\Exception $e) {
            Log::error('Exception during insertarServiciosReserva: ' . $e->getMessage());
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

    public function obtenerServiciosReserva($reservaID) {
        try {
            // Validar que la reservaID sea un entero válido
            if (!is_numeric($reservaID)) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error de validación',
                        'error' => 'La reservaID debe ser un entero válido'
                    ], 400
                );
            }
    
            // Obtener información de la reserva
            $reserva = DB::table('reservas')
                ->where('id', $reservaID)
                ->first();
    
            if (!$reserva) {
                return response()->json(
                    [
                        'status' => 404,
                        'data' => [],
                        'msg' => 'No se encontró la reserva especificada',
                        'error' => []
                    ], 404
                );
            }
    
            // Obtener habitaciones asociadas a la reserva
            $habitacionesReservas = DB::table('habitaciones_reservas as hr')
                ->join('habitaciones as h', 'hr.habitacionID', '=', 'h.id')
                ->join('tipo_habitacion as th', 'h.tipoID', '=', 'th.id')
                ->where('hr.reservaID', $reservaID)
                ->select('th.tipo', 
                        'hr.id as habitacionReservaID', 
                        'h.id as habitacionID', 
                        'h.numero',)
                ->get();
    
            $response = [
                'reservaID' => $reserva->id,
                'fecha_entrada' => $reserva->fecha_entrada,
                'fecha_salida' => $reserva->fecha_salida,
                'habitaciones' => []
            ];
    
            // Iterar sobre las habitaciones y obtener los servicios asociados
            foreach ($habitacionesReservas as $habitacionReserva) {
                $servicios = DB::table('servicios_reservas as sr')
                    ->join('servicios as s', 'sr.servicioID', '=', 's.id')
                    ->join('tipo_servicio as ts', 's.tipoID', '=', 'ts.id')
                    ->where('sr.habitacionReservaID', $habitacionReserva->habitacionReservaID)
                    ->select('s.nombre as servicioNombre', 'sr.cantidad', 'ts.tipo')
                    ->get();
    
                // Agregar la información de la habitación y sus servicios a la respuesta
                $response['habitaciones'][] = [
                    'habitacionID' => $habitacionReserva->habitacionID,
                    'tipoHabitacion' => $habitacionReserva->tipo,
                    'numeroHabitacion' => $habitacionReserva->numero,
                    'servicios' => $servicios
                ];
            }
    
            return response()->json(
                [
                    'status' => 200,
                    'data' => $response,
                    'msg' => 'Servicios obtenidos con éxito',
                    'error' => []
                ], 200
            );
    
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
