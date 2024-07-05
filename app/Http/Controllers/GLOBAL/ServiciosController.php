<?php

namespace App\Http\Controllers\GLOBAL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Servicio;
use App\Models\TipoServicio;

class ServiciosController extends Controller
{
    public function crearTipoServicio(Request $request){
        $validation = Validator::make($request->all(), [
            'tipo' => 'required|string|max:50',
        ]);

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

        $tipo_servicio = TipoServicio::create([
            'tipo' => $request->tipo,
            'status' => 1
        ]);

        return response()->json(
            [
                'status' => 200,
                'data' => $tipo_servicio,
                'msg' => 'Tipo de Servicio creado con éxito',
                'error' => []
            ], 200
        );

    }

    public function crearServicio(Request $request){
        $validation = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'tipoID' => 'required|integer'
        ]);

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

        $servicio = Servicio::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'tipoID' => $request->tipoID
        ]);

        return response()->json(
            [
                'status' => 200,
                'data' => $servicio,
                'msg' => 'Servicio creado con éxito',
                'error' => []
            ], 200
        );
    }

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

    public function obtenerServiciosReserva($id){
        try{
            

        } catch(\Exception $e){
            Log::error('Exception during obtenerServiciosReserva: ' . $e->getMessage());
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
