<?php

namespace App\Http\Controllers\GLOBAL;

use App\Events\accessEvent;
use App\Events\nfcEvent;
use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\Tarjeta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TarjetasController extends Controller
{
    public function crearTarjeta(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'data.id' => 'required',
                'data.tipoID' => 'required|integer',
            ]);
            // Verificar si la tarjeta ya existe
            $tarjeta = Tarjeta::where('id', $validatedData['data']['id'])->first();
            if ($tarjeta) {
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'La tarjeta ya existe.'
                ], 400);
            }


            // Crear la tarjeta en la base de datos
            $tarjeta = Tarjeta::create([
                'id' => $validatedData['data']['id'],
                'tipoID' => $validatedData['data']['tipoID'],
                'status' => 1,
            ]);

            return response()->json([
                'status' => 200,
                'data' => $tarjeta,
                'msg' => 'Tarjeta creada con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'data' => [],
                'msg' => 'Hubo un error al crear la tarjeta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexTarjeta()
    {
        $tarjetas = Tarjeta::with('tipoTarjeta', 'reservas', 'limpiezas')->get();
        return response()->json([
            'status' => 200,
            'data' => $tarjetas,
            'msg' => 'Tarjetas obtenidas con éxito.'
        ]);
    }

    public function validarTarjeta(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    "tarjeta" => "required",
                    "habitacionID" => "required",
                ]
            );

            if ($validation->fails()) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Todos los campos son necesarios y deben cumplir con el formato adecuado.',
                        'error' => $validation->errors()
                    ],
                    400
                );
            }

            $numeroTarjeta = $request->tarjeta;
            $habitacionId = $request->habitacionID;
            $fechaHoraActual = Carbon::now('America/Monterrey');
            $fechaActual = $fechaHoraActual->toDateString(); 
            $horaActual = $fechaHoraActual->toTimeString(); 
            $numeroHabitacion = Habitacion::where('id', $habitacionId)->first();


            $infoTarjeta = DB::table('tarjetas as t')
                ->join('tipo_tarjeta as tt', 'tt.id', '=', 't.tipoID')
                ->select('t.id', 't.numero as UID', 'tt.tipo', 't.status')
                ->where('t.numero', $numeroTarjeta)
                ->first();



            if (!$infoTarjeta) {
                return response()->json([
                    'status' => 200,
                    'data' => false,
                    'msg' => 'Tarjeta no encontrada.'
                ]);
            }

            if(!$numeroHabitacion){
                return response()->json([
                    'status' => 200,
                    'data' => false,
                    'msg' => 'Habitación no encontrada.'
                ]);
            }
            $numeroHabitacion = $numeroHabitacion->numero;
            //dd($infoTarjeta);

            $statusTarjeta = $infoTarjeta->status;
            $tipoTarjeta = $infoTarjeta->tipo;

            //validar si la tarjeta esta activa
            if ($statusTarjeta == 0) {
                $huesped = DB::table('reservas')
                    ->join('habitaciones_reservas', 'reservas.id', '=', 'habitaciones_reservas.reservaID')
                    ->join('huespedes', 'reservas.huespedID', '=', 'huespedes.id')
                    ->select('huespedes.id as huespedID')
                    ->where('habitaciones_reservas.habitacionID', $habitacionId)
                    ->where('reservas.status', 1)
                    ->whereRaw('? BETWEEN reservas.fecha_entrada AND reservas.fecha_salida', [$fechaActual])
                    ->first();

                
                if ($huesped) {
                    if ($tipoTarjeta == 'Huesped') {
                        $id=$huesped->huespedID;
                        nfcEvent::dispatch('Un huesped ha intentado entrar a la habitación ' . $numeroHabitacion, $id);
                        return response()->json([
                            'status' => 200,
                            'data' => [
                                "acceso" => false,
                                "UID" => $numeroTarjeta,
                                "tipo_tarjeta" => $tipoTarjeta,
                                "habitacion" => $numeroHabitacion,
                                "fecha" => $fechaActual,
                                "hora" => $horaActual
                            ],
                            'msg' => 'Un huesped ha intentado entrar a la habitación'
                        ]);
                    } else if ($tipoTarjeta == 'Limpieza') {
                        $id=$huesped->huespedID;
                        nfcEvent::dispatch('Un personal de limpieza ha intentado entrar a la habitación ' . $numeroHabitacion, $id);
                        return response()->json([
                            'status' => 200,
                            'data' => [
                                "acceso" => false,
                                "UID" => $numeroTarjeta,
                                "tipo_tarjeta" => $tipoTarjeta,
                                "habitacion" => $numeroHabitacion,
                                "fecha" => $fechaActual,
                                "hora" => $horaActual
                            ],
                            'msg' => 'Un personal de limpieza ha intentado entrar a la habitación'
                        ]);
                    }
                } else {
                    if ($tipoTarjeta == 'Huesped') {
                        accessEvent::dispatch('Un tarjeta de huesped desactivada a intentado abrir la habitación ' . $numeroHabitacion);
                        return response()->json([
                            'status' => 200,
                            'data' => [
                                "acceso" => false,
                                "UID" => $numeroTarjeta,
                                "tipo_tarjeta" => $tipoTarjeta,
                                "habitacion" => $numeroHabitacion,
                                "fecha" => $fechaActual,
                                "hora" => $horaActual
                            ],
                            'msg' => 'Un huesped ha intentado entrar a la habitación'
                        ]);
                    } else if ($tipoTarjeta == 'Limpieza') {
                        accessEvent::dispatch('Un tarjeta del personal de limpieza  desactivada a intentado abrir la habitación ' . $numeroHabitacion);
                        return response()->json([
                            'status' => 200,
                            'data' => [
                                "acceso" => false,
                                "UID" => $numeroTarjeta,
                                "tipo_tarjeta" => $tipoTarjeta,
                                "habitacion" => $numeroHabitacion,
                                "fecha" => $fechaActual,
                                "hora" => $horaActual
                            ],
                            'msg' => 'Un personal de limpieza ha intentado entrar a la habitación'
                        ]);
                    }
                }
            }

            //si la tarjeta es administrativa se le pemrite la entrada
            if ($tipoTarjeta == 'Administrativa') {
                $huesped = DB::table('reservas')
                    ->join('habitaciones_reservas', 'reservas.id', '=', 'habitaciones_reservas.reservaID')
                    ->join('huespedes', 'reservas.huespedID', '=', 'huespedes.id')
                    ->select('huespedes.id as huespedID')
                    ->where('habitaciones_reservas.habitacionID', $habitacionId)
                    ->where('reservas.status', 1)
                    ->whereRaw('? BETWEEN reservas.fecha_entrada AND reservas.fecha_salida', [$fechaActual])
                    ->first();
                
                if ($huesped) {
                    $id = $huesped->huespedID;
                    nfcEvent::dispatch('Un administrador ha entrado a la habitación ' . $numeroHabitacion, $id);
                    return response()->json([
                        'status' => 200,
                        'data' => [
                            "acceso" => true,
                            "UID" => $numeroTarjeta,
                            "tipo_tarjeta" => $tipoTarjeta,
                            "habitacion" => $numeroHabitacion,
                            "fecha" => $fechaActual,
                            "hora" => $horaActual
                        ],
                        'msg' => 'Un administrador ha entrado a la habitación'
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'data' => [
                            "acceso" => true,
                            "UID" => $numeroTarjeta,
                            "tipo_tarjeta" => $tipoTarjeta,
                            "habitacion" => $numeroHabitacion,
                            "fecha" => $fechaActual,
                            "hora" => $horaActual
                        ],
                        'msg' => 'Un administrador ha entrado a la habitación'
                    ]);
                }
            }

            //consultar si la tarjeta tiene pemriso de ingresar o no
            $consulta1 = DB::table('reservas as r')
                ->join('habitaciones_reservas as hr', 'hr.reservaID', '=', 'r.id')
                ->join('habitaciones as h', 'h.id', '=', 'hr.habitacionID')
                ->join('tipo_habitacion as th', 'th.id', '=', 'h.tipoID')
                ->join('tarjetas_reservas as tr', 'tr.reservaID', '=', 'r.id')
                ->join('tarjetas as t', 't.id', '=', 'tr.tarjetaID')
                ->join('tipo_tarjeta as tt', 'tt.id', '=', 't.tipoID')
                ->select(
                    'r.id',
                    'r.fecha_entrada',
                    'r.fecha_salida',
                    'hr.habitacionID',
                    'h.numero',
                    'th.tipo',
                    'tr.tarjetaID',
                    't.numero as UID',
                    'tt.tipo as tipo_tarjeta'
                )
                ->whereRaw('? BETWEEN r.fecha_entrada AND r.fecha_salida', [$fechaActual])
                ->where('r.status', 1)
                ->where('t.numero', $numeroTarjeta)
                ->where('h.id', $habitacionId)
                ->where('t.status', 1);

            $consulta2 = DB::table('reservas as r')
                ->join('habitaciones_reservas as hr', 'hr.reservaID', '=', 'r.id')
                ->join('habitaciones as h', 'h.id', '=', 'hr.habitacionID')
                ->join('tipo_habitacion as th', 'th.id', '=', 'h.tipoID')
                ->join('limpiezas as l', 'l.habitacion_reservaID', '=', 'hr.id')
                ->join('tarjetas as t', 't.id', '=', 'l.tarjetaID')
                ->join('tipo_tarjeta as tt', 'tt.id', '=', 't.tipoID')
                ->select(
                    'r.id',
                    'r.fecha_entrada',
                    'r.fecha_salida',
                    'hr.habitacionID',
                    'h.numero',
                    'th.tipo',
                    'l.tarjetaID',
                    't.numero as UID',
                    'tt.tipo as tipo_tarjeta'
                )
                ->whereRaw('? BETWEEN r.fecha_entrada AND r.fecha_salida', [$fechaActual])
                ->where('r.status', 1)
                ->where('l.status', 1)
                ->whereDate('l.fecha', $fechaActual)
                ->where('t.numero', $numeroTarjeta)
                ->where('h.id', $habitacionId);

            $resultados = $consulta1->union($consulta2)->get();

            // validar si hay resultados de la consulta
            if ($resultados->isNotEmpty()) {
                $reservaID = $resultados[0]->id;
                $habitacionId = $resultados[0]->habitacionID;
                $huespedID = Reserva::where('id', $reservaID)->first()->huespedID;
                $numeroHabitacion = $resultados[0]->numero;

                if ($tipoTarjeta == 'Huesped') {
                    nfcEvent::dispatch('Un huesped ha entrado a la habitacción ' . $numeroHabitacion, $huespedID);
                    //Si la tarjeta es huesped y hay resultados de la consulta, entonces se le permite la entrada
                    return response()->json([
                        'status' => 200,
                        'data' => [
                            "acceso" => true,
                            "UID" => $numeroTarjeta,
                            "tipo_tarjeta" => $tipoTarjeta,
                            "habitacion" => $numeroHabitacion,
                            "fecha" => $fechaActual,
                            "hora" => $horaActual
                        ],
                        'msg' => 'Un huesped ha entrado a la habitación.'
                    ]);
                } else if ($tipoTarjeta == 'Limpieza') {
                    nfcEvent::dispatch('Un personal de limpieza ha entrado a la habitación ' . $numeroHabitacion, $huespedID);
                    //Si la tarjeta es de limpieza entonces se le permite la entrada
                    return response()->json([
                        'status' => 200,
                        'data' => [
                            "acceso" => true,
                            "UID" => $numeroTarjeta,
                            "tipo_tarjeta" => $tipoTarjeta,
                            "habitacion" => $numeroHabitacion,
                            "fecha" => $fechaActual,
                            "hora" => $horaActual
                        ],
                        'msg' => 'Un personal de limpieza ha entrado a la habitación.'
                    ]);
                }
            }

            $huesped = DB::table('reservas')
                ->join('habitaciones_reservas', 'reservas.id', '=', 'habitaciones_reservas.reservaID')
                ->join('huespedes', 'reservas.huespedID', '=', 'huespedes.id')
                ->select('huespedes.id as huespedID')
                ->where('habitaciones_reservas.habitacionID', $habitacionId)
                ->where('reservas.status', 1)
                ->whereRaw('? BETWEEN reservas.fecha_entrada AND reservas.fecha_salida', [$fechaActual])
                ->first();
            
            if($huesped){
                if ($tipoTarjeta == 'Huesped') {
                    $id=$huesped->huespedID;

                    nfcEvent::dispatch('Un huesped ha intentado entrar a la habitación ' . $numeroHabitacion, $id);
                    return response()->json([
                        'status' => 200,
                        'data' => [
                            "acceso" => false,
                            "UID" => $numeroTarjeta,
                            "tipo_tarjeta" => $tipoTarjeta,
                            "habitacion" => $numeroHabitacion,
                            "fecha" => $fechaActual,
                            "hora" => $horaActual
                        ],
                        'msg' => 'Un huesped ha intentado entrar a la habitación'
                    ]);
                } else if ($tipoTarjeta == 'Limpieza') {
                    $id=$huesped->huespedID;
                    nfcEvent::dispatch('Un personal de limpieza ha intentado entrar a la habitación ' . $numeroHabitacion, $id);
                    return response()->json([
                        'status' => 200,
                        'data' => [
                            "acceso" => false,
                            "UID" => $numeroTarjeta,
                            "tipo_tarjeta" => $tipoTarjeta,
                            "habitacion" => $numeroHabitacion,
                            "fecha" => $fechaActual,
                            "hora" => $horaActual
                        ],
                        'msg' => 'Un personal de limpieza ha intentado entrar a la habitación'
                    ]);
                }
                else if ($tipoTarjeta == 'Administrativa') {
                    $id=$huesped->huespedID;
                    nfcEvent::dispatch('Un administrador ha entrado a la habitación ' . $numeroHabitacion, $id);
                    return response()->json([
                        'status' => 200,
                        'data' => [
                            "acceso" => true,
                            "UID" => $numeroTarjeta,
                            "tipo_tarjeta" => $tipoTarjeta,
                            "habitacion" => $numeroHabitacion,
                            "fecha" => $fechaActual,
                            "hora" => $horaActual
                        ],
                        'msg' => 'Un administrador ha entrado a la habitación'
                    ]);
                }
            }

            if($tipoTarjeta == 'Huesped'){
                accessEvent::dispatch('Un huesped ha intentado entrar a la habitación' . $numeroHabitacion);
                return response()->json([
                    'status' => 200,
                    'data' => [
                        "acceso" => false,
                        "UID" => $numeroTarjeta,
                        "tipo_tarjeta" => $tipoTarjeta,
                        "habitacion" => $numeroHabitacion,
                        "fecha" => $fechaActual,
                        "hora" => $horaActual
                    ],
                    'msg' => 'Un huesped ha intentado entrar a la habitación'
                ]);
            } else if($tipoTarjeta == 'Limpieza'){
                accessEvent::dispatch('Un personal de limpieza ha intentado entrar a la habitación' . $numeroHabitacion);
                return response()->json([
                    'status' => 200,
                    'data' => [
                        "acceso" => false,
                        "UID" => $numeroTarjeta,
                        "tipo_tarjeta" => $tipoTarjeta,
                        "habitacion" => $numeroHabitacion,
                        "fecha" => $fechaActual,
                        "hora" => $horaActual
                    ],
                    'msg' => 'Un personal de limpieza ha intentado entrar a la habitación'
                ]);
            } else if($tipoTarjeta == 'Administrativa'){
                accessEvent::dispatch('Un administrador ha entrado a la habitación' . $numeroHabitacion);
                return response()->json([
                    'status' => 200,
                    'data' => [
                        "acceso" => true,
                        "UID" => $numeroTarjeta,
                        "tipo_tarjeta" => $tipoTarjeta,
                        "habitacion" => $numeroHabitacion,
                        "fecha" => $fechaActual,
                        "hora" => $horaActual
                    ],
                    'msg' => 'Un administrador ha entrado a la habitación'
                ]);
            }
            
            return response()->json([
                'status' => 200,
                'data' => false,
                'msg' => 'No se encontraron reservas activas para la tarjeta.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception during validarTarjeta: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'data' => [],
                'msg' => 'Hubo un error al validar la tarjeta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
