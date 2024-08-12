<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Tarjeta;
use App\Models\TipoTarjeta;
use Carbon\Carbon; 

class TarjetasController extends Controller
{
    //funcion para mostrar todas las tarjetas
    public function traerTarjetas(){
        $tarjetas = Tarjeta::select('tarjetas.id', 'tarjetas.numero', 'tarjetas.tipoID', 'tarjetas.status', 'tipo_tarjeta.tipo')
            ->join('tipo_tarjeta', 'tipo_tarjeta.id', '=', 'tarjetas.tipoID')
            ->orderBy('tipo_tarjeta.tipo')
            ->get();

        return response()->json(
            [
                'status' => 200,
                'data' => $tarjetas,
                'msg' => 'Servicios solicitados obtenidos correctamente',
                'error' => []
            ], 200
        );
    }

    public function crearTarjeta(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "numero" => "required|unique:tarjetas,numero",
                    "tipoID" => "required",
                ],
                [
                    'numero.required' => 'El campo :attribute es obligatorio.',
                    'numero.unique' => 'La tarjeta ya se encuentra registrada.',
                    'tipoID.required' => 'El campo :attribute es obligatorio.',
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

            $tarjeta = Tarjeta::create([
                "numero" => $request->numero,
                "tipoID" => $request->tipoID,
                "status" => 1,
            ]);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $tarjeta,
                    'msg' => 'Tarjeta creada con éxito.',
                    'error' => []
                ], 200
            );
        } catch(\Exception $e){
            Log::error('Exception during crearTarjeta: ' . $e->getMessage());
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

    public function editTarjeta(Request $request, $id){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "tipoID" => "required",
                ],
                [
                    'tipoID.required' => 'El campo :attribute es obligatorio.',
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

            $tarjeta = Tarjeta::find($id);
            if($tarjeta){
                $tarjeta->tipoID = $request->tipoID;

                if($tarjeta->save()){
                    return response()->json(
                        [
                            'status' => 200,
                            'data' => $tarjeta,
                            'msg' => 'Tarjeta actualizada con éxito.',
                            'error' => []
                        ], 200
                    );
                } else{
                    return response()->json(
                        [
                            'status' => 500,
                            'data' => [],
                            'msg' => 'Error al actualizar la tarjeta.',
                            'error' => []
                        ], 500
                    );

                }
            } else{
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Tarjeta no encontrada.',
                        'error' => []
                    ], 400
                );
            }
        } catch(\Exception $e){
            Log::error('Exception during editTarjeta: ' . $e->getMessage());
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

    public function cambiarStatus(Request $request, $id){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "status" => "required",
                ],
                [
                    'status.required' => 'El campo :attribute es obligatorio.',
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

            $tarjeta = Tarjeta::find($id);
            if($tarjeta){
                $tarjeta->status = $request->status;

                if($tarjeta->save()){
                    return response()->json(
                        [
                            'status' => 200,
                            'data' => $tarjeta,
                            'msg' => 'Status de tarjeta actualizado con éxito.',
                            'error' => []
                        ], 200
                    );
                } else{
                    return response()->json(
                        [
                            'status' => 500,
                            'data' => [],
                            'msg' => 'Error al actualizar el status de la tarjeta.',
                            'error' => []
                        ], 500
                    );

                }
            } else{
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Tarjeta no encontrada.',
                        'error' => []
                    ], 400
                );
            }
        } catch(\Exception $e){
            Log::error('Exception during cambiarStatusTarjeta: ' . $e->getMessage());
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

    public function obtenerTiposTarjetas(){
        try{
            $tiposTarjetas = TipoTarjeta::all();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $tiposTarjetas,
                    'msg' => 'Tipos de tarjetas obtenidas correctamente',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during obtenerTiposTarjetas: ' . $e->getMessage());
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

    function asignarTarjetaReserva(Request $request, $reservaID){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "tarjeta" => "required",
                ],
                [
                    'tarjeta.required' => 'El campo :attribute es obligatorio.',
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

            $UID = $request->tarjeta;
            $hoy = Carbon::now('America/Monterrey')->toDateString();

            $tarjeta = DB::table(DB::raw('(
                SELECT
                    MAX(t.id) as id,
                    MAX(t.numero) as UID,
                    MAX(tt.tipo) as tipo,
                    MAX(t.status) as status,
                    MIN(
                        CASE 
                            WHEN r.status = 0 THEN 1
                            WHEN tr.status = 0 THEN 1
                            WHEN ? BETWEEN r.fecha_entrada AND r.fecha_salida THEN 0
                            WHEN ? = l.fecha THEN 0 
                            ELSE 1
                        END
                    ) as disponibilidadBool
                FROM tarjetas t
                LEFT JOIN tipo_tarjeta tt ON t.tipoID = tt.id
                LEFT JOIN tarjetas_reservas tr ON tr.tarjetaID = t.id AND tr.status = 1
                LEFT JOIN reservas r ON r.id = tr.reservaID AND ? BETWEEN r.fecha_entrada AND r.fecha_salida AND r.status = 1
                LEFT JOIN limpiezas l ON l.tarjetaID = t.id AND l.status = 1
                LEFT JOIN habitaciones_reservas hr ON (hr.id = l.habitacion_reservaID AND ? = l.fecha) OR hr.reservaID = r.id
                GROUP BY t.id
                HAVING UID = ?
            ) as tb'))
            ->setBindings([$hoy, $hoy, $hoy, $hoy, $UID])
            ->first();
            
            if(!$tarjeta){
                return response()->json([
                    'status' => 404,
                    'data' => [],
                    'msg' => 'Tarjeta no encontrada.',
                    'error' => []
                ]);
            }

            if($tarjeta->disponibilidadBool == 0){
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'La tarjeta ya se encuentra asignada en otra reserva.',
                    'error' => []
                ]);
            }

            if($tarjeta->status == 0){
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'La tarjeta no se encuentra activa.',
                    'error' => []
                ]);
            }

            if($tarjeta->tipo == 'Limpieza'){
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'El tipo de tarjeta debe ser de Huesped para poder asignar a la reserva.',
                    'error' => []
                ]);
            }

            $tarjetaID = $tarjeta->id;
            $tarjeta_reserva = DB::table('tarjetas_reservas')->insert([
                'reservaID' => $reservaID,
                'tarjetaID' => $tarjetaID,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 200,
                'data' => $tarjeta_reserva,
                'msg' => 'Tarjeta asignada con éxito.',
                'error' => []
            ]);
        } catch (\Exception $e) {
            Log::error('Exception during asignarTarjetaReserva: ' . $e->getMessage());
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
