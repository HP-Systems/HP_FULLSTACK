<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoTarjeta;
use App\Models\Tarjeta;

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
            
            if ($tipo_tarjeta->save()) {
                Tarjeta::where('tipoID', $request->id)->update(['status' => $request->status]);
    
                return response()->json(['msg' => 'Tipo de tarjeta actualizado con éxito'], 200);
            } else {
                return response()->json(['error' => 'No se pudo actualizar el tipo de tarjeta.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception during cambiarStatusTipoTarjeta: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el tipo de tarjeta.']);
        }
    }

    public function indexTarjetas(Request $request){
        $filtro = $request->input('filtro', 'todas');
        $tipo = $request->input('tipo', 'todos');
        
        $query = DB::table(DB::raw('(
            SELECT 
                t.id,
                t.tipoID,
                tt.tipo,
                t.status,
                tt.status as status_tipo,
                CASE 
                    WHEN r.status = 0 THEN "Disponible"
                    WHEN tr.status = 0 THEN "Disponible"
                    WHEN CURDATE() BETWEEN r.fecha_entrada AND r.fecha_salida THEN "Ocupada"
                    WHEN CURDATE() = l.fecha THEN "Ocupada"
                    ELSE "Disponible"
                END as disponibilidad,
                CASE 
                    WHEN r.status = 0 THEN 1
                    WHEN tr.status = 0 THEN 1
                    WHEN CURDATE() BETWEEN r.fecha_entrada AND r.fecha_salida THEN 0
                    WHEN CURDATE() = l.fecha THEN 1
                    ELSE 1
                END as disponibilidadBool
            FROM tarjetas t
            LEFT JOIN tipo_tarjeta tt ON t.tipoID = tt.id
            LEFT JOIN tarjetas_reservas tr ON tr.tarjetaID = t.id AND tr.status = 1
            LEFT JOIN reservas r ON r.id = tr.reservaID AND CURDATE() BETWEEN r.fecha_entrada AND r.fecha_salida AND r.status = 1
            LEFT JOIN limpiezas l ON l.tarjetaID = t.id AND CURDATE() = l.fecha AND l.status = 1
            LEFT JOIN habitaciones_reservas hr ON (hr.id = l.habitacion_reservaID AND CURDATE() = l.fecha) OR hr.reservaID = r.id
        ) as tb'))
        ->selectRaw('
            MAX(id) as id,
            MAX(tipoID) as tipoID,
            MAX(tipo) as tipo,
            MAX(status) as status, 
            MAX(status_tipo) as status_tipo,
            MIN(disponibilidadBool) as disponibilidad
        ')
        ->groupBy('id');

        if ($tipo !== 'todos') {
            $query->where('tipoID', '=', $tipo);
        }

        if ($filtro == 'ocupadas') {
            $query->having('disponibilidad', '=', 0);
        } elseif ($filtro == 'disponibles') {
            $query->having('disponibilidad', '=', 1);
        }

        $tarjetas = $query->get();
        $tipos = TipoTarjeta::all();

        if ($request->ajax()) {
            return view('tarjetas.cards_tarjetas', compact('tarjetas'))->render();
        }

        return view('tarjetas.tarjetas', compact('tarjetas', 'tipos'));
    }

    public function cambiarStatusTarjeta(Request $request){
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

            $tipo_tarjeta = Tarjeta::find($request->id);
            $tipo_tarjeta->status = $request->status;
            
            if ($tipo_tarjeta->save()) {
                return response()->json(['msg' => 'Tarjeta actualizada con éxito'], 200);
            } else {
                return response()->json(['error' => 'No se pudo actualizar la tarjeta.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception during cambiarStatusTarjeta: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar la tarjeta']);
        }
    }
}
