<?php

namespace App\Http\Controllers\MOVIL;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HotelController extends Controller
{
    public function hotelIndex(Request $request)
    {
        try{
            $hotel = Hotel::first();
            return response()->json($hotel, 200);
            
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
        catch (\PDOException $e) {
            Log::error($e->getMessage());
        }
    }
    public function habitaciones()
    {
        $habitaciones = Habitacion::with('tipoHabitacion')->get();
        $habitaciones->map(function ($habitacion) {
            $habitacion->tipo = $habitacion->tipoHabitacion->tipo;
            $habitacion->capacidad = $habitacion->tipoHabitacion->capacidad;
            $habitacion->precio_noche = $habitacion->tipoHabitacion->precio_noche;
            $habitacion->descripcion = $habitacion->tipoHabitacion->descripcion;
            unset($habitacion->tipoHabitacion);
            return $habitacion;
        });
        return response()->json(
            [
                'data'=>$habitaciones,
                'msg' => 'Habitaciones obtenidas con éxito',
                'status' => 200,
                ]
        );
        
    }
}
