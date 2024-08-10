<?php

namespace App\Http\Controllers\DESKTOP;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Tarjeta;
use App\Models\TipoTarjeta;

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
    }

    public function editTarjeta(Request $request, $id){
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
    }

    public function cambiarStatus(Request $request, $id){
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
