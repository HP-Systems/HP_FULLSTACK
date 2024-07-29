<?php

namespace App\Http\Controllers\GLOBAL;

use App\Http\Controllers\Controller;
use App\Models\Tarjeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TarjetasController extends Controller
{
    public function crearTarjeta(Request $request)
    {
        try{
        // Validar los datos de entrada
        $validatedData = $request->validate([
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
    }
    catch (\Exception $e) {
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
}
