<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\TipoServicio;

class ServiciosController extends Controller
{
    public function index(){
        $servicios = DB::table('servicios as s')
            ->join('tipo_servicio as ts', 'ts.id', '=', 's.tipoID')
            ->selectRaw('s.id, s.nombre, s.descripcion, s.precio, s.status, s.tipoID, ts.tipo') 
            ->get();

        $tipos = TipoServicio::where('status', '=', 1)->get();

        return view('servicios.servicios', compact('servicios', 'tipos'));
    }

    public function cambiarStatus(Request $request){
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

            $service = Servicio::where('id', $request->id)->first();
            $service->status = $request->status;
            $service->save();

            return response()->json(['msg' => 'Servicio actualizado con éxito'], 200);
        } catch(\Exception $e){
            Log::error('Exception during cambiarStatus: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error de servidor.']);
        }
    }

    public function insertService(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    'name_servicio' => 'required',
                    'descripcion' => 'required',
                    'precio' => 'required',
                    'tipo' => 'required'
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $service = Servicio::create([
                'nombre' => $request->name_servicio,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'tipoID' => $request->tipo,
            ]);
   
            return response()->json(['msg' => 'Servicio creado con éxito'], 200);
        } catch(\Exception $e){
            Log::error('Exception during insertService: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al crear el servicio.']);
        }
    }

    public function editService(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    'name_servicio' => 'required',
                    'descripcion' => 'required',
                    'precio' => 'required',
                    'tipo' => 'required'
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $service = Servicio::find($request->id);
            $service->nombre = $request->name_servicio;
            $service->descripcion = $request->descripcion;
            $service->precio = $request->precio;
            $service->tipoID = $request->tipo;
            $service->save();

            return response()->json(['edit' => 'Servicio actualizado con éxito'], 200);
    
        } catch(\Exception $e){
            Log::error('Exception during editService: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el servicio.']);
        }
    }
}
