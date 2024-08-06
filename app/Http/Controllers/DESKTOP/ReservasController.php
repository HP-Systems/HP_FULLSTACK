<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use App\Models\Huesped;
use App\Models\Reserva;
use App\Models\Hauesped;
use App\Mail\passwordMail;
use App\Models\User;
use App\Rules\UniqueEmailForUserableType;

class ReservasController extends Controller
{
    public function traerReservas($fecha1 = null, $fecha2 = null){
        try{
            $fechaActual = Carbon::today()->toDateString();

            if (is_null($fecha1) && is_null($fecha2)) {
                $fecha1 = $fechaActual;
                $fecha2 = $fechaActual;
            }

            $fechaInicio = $fecha1;
            $fechaFin = $fecha2;

            $reservas = Reserva::with(['huesped.user' => function($query){
            $query->where('userable_type', 2);
            }])
            ->where(function($query) use ($fechaInicio, $fechaFin) {
            $query->where('fecha_entrada', '<=', $fechaFin)
                    ->where('fecha_salida', '>=', $fechaInicio);
            })
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

            $reservasConOrden = $reservasConHabitaciones->map(function ($reserva) {
                $nuevaReserva = [
                    'id' => $reserva->id,
                    'huespedID' => $reserva->huespedID,
                    'fecha_entrada' => $reserva->fecha_entrada,
                    'fecha_salida' => $reserva->fecha_salida,
                    'hora_entrada' => $reserva->hora_entrada,
                    'hora_salida' => $reserva->hora_salida,
                    'status' => $reserva->status,
                    'huesped' => [
                        'id' => $reserva->huesped->id,
                        'nombre' => $reserva->huesped->nombre,
                        'apellido' => $reserva->huesped->apellido,
                        'telefono' => $reserva->huesped->telefono,
                        'user' => [
                            'id' => $reserva->huesped->user->id ? $reserva->huesped->user->id : null,
                            'email' => $reserva->huesped->user->email ? $reserva->huesped->user->email : null,
                            'userable_id' => $reserva->huesped->user->userable_id ? $reserva->huesped->user->userable_id : null,
                            'userable_type' => $reserva->huesped->user->userable_type ? $reserva->huesped->user->userable_type : null,
                            'status' => $reserva->huesped->user->status ? $reserva->huesped->user->status : null,
                        ]
                    ],
                    'habitaciones' => $reserva->habitaciones->map(function ($habitacion) {
                        return [
                            'id' => $habitacion->id,
                            'numero' => $habitacion->numero,
                            'tipoID' => $habitacion->tipoID,
                            'imagen' => $habitacion->imagen,
                            'status' => $habitacion->status,
                            'tipo_habitacion' => [
                                'id' => $habitacion->tipo_habitacion->id,
                                'tipo' => $habitacion->tipo_habitacion->tipo,
                                'capacidad' => $habitacion->tipo_habitacion->capacidad,
                                'precio_noche' => $habitacion->tipo_habitacion->precio_noche,
                                'descripcion' => $habitacion->tipo_habitacion->descripcion,
                                'status' => $habitacion->tipo_habitacion->status,
                            ]
                        ];
                    }),
                ];

                return $nuevaReserva;
            });

            return response()->json(
                [
                    'status' => 200,
                    'data' => $reservasConOrden,
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

    public function createReserva(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "fecha_entrada" => "required",
                    "fecha_salida" => "required",
                    "huesped" => "required|array",
                    "huesped.nombre" => "required",
                    "huesped.apellido" => "required",
                    "huesped.telefono" => "required|numeric|digits:10",
                    "huesped.email" => ["required", "email", new UniqueEmailForUserableType(2)],
                    "habitaciones" => "required|array",
                ],
                [
                    'huesped.telefono.numeric' => 'El teléfono debe contener solo números.',
                    'huesped.telefono.digits' => 'El teléfono debe tener un formato válido y contener exactamente 10 dígitos.',
                ]
                
            );

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

            $huespedID = $request->huesped['id'] ?? 0;

            if($huespedID == 0 || $huespedID == null){
                $huesped = Huesped::create([
                    "nombre" => $request->huesped['nombre'],
                    "apellido" => $request->huesped['apellido'],
                    "telefono" => $request->huesped['telefono'],
                ]);


                $huespedID = $huesped->id;
                $password = Str::random(9);

                $user = User::create([
                    "email" => $request->huesped['email'],
                    "password" => Hash::make($password),
                    "userable_id" => $huesped->id,
                    "userable_type" => 2,
                ]);

                #Mail::to($request->email)->send(new passwordMail($user, $password));
            }

            $reserva = Reserva::create([
                'huespedID' => $huespedID,
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
