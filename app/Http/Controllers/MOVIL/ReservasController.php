<?php

namespace App\Http\Controllers\MOVIL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Reserva;
use App\Models\HabitacionReserva;

class ReservasController extends Controller
{
    public function createReserva(Request $request){
        /*Formato esperado
            {
                "fecha_entrada" : "2024-07-02",
                "fecha_salida" : "2024-07-10",
                "huespedID" : 1,
                "habitaciones" : [
                    {
                        "id" : "1"
                    },
                    {
                        "id" : "5"
                    }
                ]
            }
        */
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

            if (is_array($request->habitaciones) && count($request->habitaciones) > 0) {
                foreach ($request->habitaciones as $habitacion) {
                    if (isset($habitacion['id'])) {
                        DB::table('habitaciones_reservas')->insert([
                            'reservaID' => $reserva->id,
                            'habitacionID' => $habitacion['id'],
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

    public function obtenerReservasHuesped($idUser){
        try{
            if (!is_numeric($idUser) || (int)$idUser <= 0) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'El ID proporcionado no es válido.',
                        'error' => ['El ID debe ser un número entero positivo.']
                    ], 400
                );
            }

            $hoy = Carbon::today()->toDateString();

            $reservas = Reserva::with('habitaciones')
                    ->where('id', $idUser)
                    // Reservas en proceso
                    ->where(function($query) use ($hoy) {
                        $query->where('fecha_entrada', '<=', $hoy)
                              ->where('fecha_salida', '>=', $hoy);
                    })
                    // Reservas futuras
                    ->orWhere(function($query) use ($hoy) {
                        $query->where('fecha_entrada', '>=', $hoy);
                    })
                    ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $reservas,
                    'msg' => 'Reservas obtenidas con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
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

    public function obtenerReservasPasadasHuesped($idUser){
        try{
            if (!is_numeric($idUser) || (int)$idUser <= 0) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'El ID proporcionado no es válido.',
                        'error' => ['El ID debe ser un número entero positivo.']
                    ], 400
                );
            }

            $hoy = Carbon::today()->toDateString();

            $reservas = Reserva::with('habitaciones')
                    ->where('id', $idUser)
                    // Reservas pasadas
                    ->where(function($query) use ($hoy) {
                        $query->where('fecha_salida', '<', $hoy);
                    })
                    ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $reservas,
                    'msg' => 'Reservas pasadas obtenidas con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
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
