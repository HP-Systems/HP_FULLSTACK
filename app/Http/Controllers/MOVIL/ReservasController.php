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

            $now = now();
            if (is_array($request->habitaciones) && count($request->habitaciones) > 0) {
                foreach ($request->habitaciones as $habitacion) {
                    if (isset($habitacion['id'])) {
                        DB::table('habitaciones_reservas')->insert([
                            'reservaID' => $reserva->id,
                            'habitacionID' => $habitacion['id'],
                            'created_at' => $now,
                            'updated_at' => $now
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
            Log::error('Exception during login: ' . $e->getMessage());
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
            Log::error('Exception during login: ' . $e->getMessage());
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
            Log::error('Exception during login: ' . $e->getMessage());
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

    public function editarReservaHabitaciones(Request $request, $idreserva){
        try{
            /*{
                "habitaciones" : [
                    {
                        "tipoID": 6,
                        "cantidad": 2
                    },
                    {
                        "tipoID": 2,
                        "cantidad": 1
                    }
                ]
            }*/

            if (!is_numeric($idreserva) || (int)$idreserva <= 0) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'El ID proporcionado no es válido.',
                        'error' => ['El ID debe ser un número entero positivo.']
                    ], 400
                );
            }
    
            $validation = Validator::make(
                $request->all(),
                [
                    "habitaciones" => "required|array",
                ]
            );
    
            if ($validation->fails()) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error de validacion',
                        'error' => $validation->errors()
                    ], 400
                );
            }

            $reserva = Reserva::find($idreserva);
            $fechaEntrada = $reserva->fecha_entrada;
            $fechaSalida = $reserva->fecha_salida;
    
            $habitacionesViejas = DB::table('habitaciones_reservas as hr')
                ->join('habitaciones as h', 'hr.habitacionID', '=', 'h.id')
                ->join('tipo_habitacion as th', 'h.tipoID', '=', 'th.id')
                ->where('hr.reservaID', '=', $idreserva)
                ->select('th.id as tipoID', 'th.tipo', DB::raw('count(*) as cantidad'))
                ->groupBy('h.tipoID')
                ->get()
                ->keyBy('tipoID');
    
            $habitacionesNuevas = collect($request->habitaciones)->keyBy('tipoID');
    
            $habitacionesIguales = [];
            $habitacionesAgregar = [];
            $habitacionesQuitar = [];
    
            //Buscar cuantas habitaciones son iguales, se agregaran o se quitaran
            foreach ($habitacionesNuevas as $tipoID => $habitacionNueva) {
                if (isset($habitacionesViejas[$tipoID])) {
                    $cantidadVieja = $habitacionesViejas[$tipoID]->cantidad;
                    $cantidadNueva = $habitacionNueva['cantidad'];
    
                    if ($cantidadNueva < $cantidadVieja) {
                        $habitacionesQuitar[] = [
                            'tipoID' => $tipoID,
                            'cantidad' => $cantidadVieja - $cantidadNueva
                        ];
                    } elseif ($cantidadNueva > $cantidadVieja) {
                        $habitacionesAgregar[] = [
                            'tipoID' => $tipoID,
                            'cantidad' => $cantidadNueva - $cantidadVieja
                        ];
                    } else {
                        $habitacionesIguales[] = [
                            'tipoID' => $tipoID,
                            'cantidad' => $cantidadNueva
                        ];
                    }
    
                    unset($habitacionesViejas[$tipoID]);
                } else {
                    $habitacionesAgregar[] = [
                        'tipoID' => $tipoID,
                        'cantidad' => $habitacionNueva['cantidad']
                    ];
                }
            }
    
            foreach ($habitacionesViejas as $tipoID => $habitacionVieja) {
                $habitacionesQuitar[] = [
                    'tipoID' => $tipoID,
                    'cantidad' => $habitacionVieja->cantidad
                ];
            }

            //Eliminar las habitaciones 
            foreach ($habitacionesQuitar as $habitacionQuitar) {
                $tipoID = $habitacionQuitar['tipoID'];
                $cantidad = $habitacionQuitar['cantidad'];
    
                $habitacionesIDs = DB::table('habitaciones')
                    ->where('tipoID', $tipoID)
                    ->pluck('id');
    
                $habitacionesReservas = DB::table('habitaciones_reservas')
                    ->whereIn('habitacionID', $habitacionesIDs)
                    ->where('reservaID', $idreserva)
                    ->take($cantidad)
                    ->pluck('id');
    
                DB::table('habitaciones_reservas')
                    ->whereIn('id', $habitacionesReservas)
                    ->delete();
            }

            //Agregar las nuevas habitaciones
            $now = now();
            foreach ($habitacionesAgregar as $habitacionAgregar) {
                $tipoID = $habitacionAgregar['tipoID'];
                $cantidad = $habitacionAgregar['cantidad'];
    
                $habitacionesDisponibles = DB::table('habitaciones as h')
                    ->leftJoin('habitaciones_reservas as hr', 'h.id', '=', 'hr.habitacionID')
                    ->where('h.tipoID', $tipoID)
                    ->whereNotExists(function ($query) use ($fechaEntrada, $fechaSalida) {
                        $query->select(DB::raw(1))
                            ->from('reservas as r')
                            ->join('habitaciones_reservas as hr2', 'r.id', '=', 'hr2.reservaID')
                            ->whereRaw('hr2.habitacionID = h.id')
                            ->where(function ($query2) use ($fechaEntrada, $fechaSalida) {
                                $query2->whereBetween('r.fecha_entrada', [$fechaEntrada, $fechaSalida])
                                    ->orWhereBetween('r.fecha_salida', [$fechaEntrada, $fechaSalida]);
                            });
                    })
                    ->select('h.id')
                    ->take($cantidad)
                    ->get();
    
                foreach ($habitacionesDisponibles as $habitacionDisponible) {
                    DB::table('habitaciones_reservas')->insert([
                        'reservaID' => $idreserva,
                        'habitacionID' => $habitacionDisponible->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            }

            //Obtener las nuevas habitaciones
            $reservaEditada = DB::table('habitaciones_reservas as hr')
            ->join('habitaciones as h', 'hr.habitacionID', '=', 'h.id')
            ->join('tipo_habitacion as th', 'h.tipoID', '=', 'th.id')
            ->where('hr.reservaID', $idreserva)
            ->select('th.id as tipoID', 'th.tipo', 'h.id as habitacionID', 'h.numero')
            ->get()
            ->groupBy('tipoID');


            return response()->json(
                [
                    'status' => 200,
                    'data' => $reservaEditada,
                    'msg' => 'Reservas actualizadas con éxito.',
                    'error' => []
                ], 200
            );
    
        } catch(Exception $e){
            Log::error('Exception during editarReservaHabitaciones: ' . $e->getMessage());
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
