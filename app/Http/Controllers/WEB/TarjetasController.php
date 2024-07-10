<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoTarjeta;

class TarjetasController extends Controller
{
    public function indexTipos(){
        $tipos = TipoTarjeta::all();

        return view('tarjetas.tipos_tarjetas', compact('tipos'));
    }

    public function insertTipoTarjeta(Request $request){
        try{
            $validation = Validator::make(
                $request->all(), 
                [
                    'tipo_tarjeta' => 'required',
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }
            
            $tipo_tarjeta = TipoTarjeta::create([
                'tipo' => $request->tipo_tarjeta,
                'status' => 1
            ]);

            return response()->json(['msg' => 'Tipo de tarjeta creado con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during insertTipoTarjeta: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al insertar el tipo de tarjeta.']);
        }
    }

    public function editTipoTarjeta(Request $request){
        try{
            $validation = Validator::make(
                $request->all(), 
                [
                    'tipo_tarjeta' => 'required',
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $tipo_tarjeta = TipoTarjeta::find($request->id);
            $tipo_tarjeta->tipo = $request->tipo_tarjeta;
            $tipo_tarjeta->save();

            return response()->json(['edit' => 'El tipo de tarjeta ha sido editado correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during editTipoTarjeta: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al editar el tipo de tarjeta.']);
        }
    }

    public function cambiarStatusTipo(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    'status' => 'required'
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $tipo_tarjeta = TipoTarjeta::find($request->id);
            $tipo_tarjeta->status = $request->status;
            $tipo_tarjeta->save();

            return response()->json(['msg' => 'Tipo de tarjeta actualizado con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during cambiarStatusTipoTarjeta: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el tipo de tarjeta.']);
        }
    }
}
