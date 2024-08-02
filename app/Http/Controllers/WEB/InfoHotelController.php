<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InfoHotelController extends Controller
{
    public function index(Request $request)
    {
        try{
            $hotel = Hotel::first();
            return view('configuracion.configuracion', ['hotel' => $hotel]);
            
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
        catch (\PDOException $e) {
            Log::error($e->getMessage());
        }

       
    }

    public function update(Request $request, $id)
    {
        try {
            // Buscar el hotel por ID
            $hotel = Hotel::find($id);
            if ($hotel == null) {
                return redirect()->back()->with('error', 'Hotel no encontrado');

            }

            // Diccionario de errores
            $messages = [
                'required' => 'El campo :attribute es obligatorio',
                'string' => 'El campo :attribute debe ser un texto',
                'email' => 'El campo :attribute debe ser un email válido',
                'numeric' => 'El campo :attribute debe ser un número',
                'digits' => 'El campo :attribute debe tener exactamente :digits dígitos',
                'date_format' => 'El campo :attribute debe tener un formato de hora válido',
            ];

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'direccion' => 'required|string',
                'email' => 'required|email',
                'telefono' => 'required|numeric|digits:10',
                'checkin' => 'required|date_format:H:i',
                'checkout' => 'required|date_format:H:i',
                'descripcion' => 'required',

            ], $messages);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            // Validar los datos

            // Actualizar los datos del hotel
            $hotel->nombre = $request->nombre;
            $hotel->direccion = $request->direccion;
            $hotel->email = $request->email;
            $hotel->telefono = $request->telefono;
            $hotel->checkin = $request->checkin;
            $hotel->checkout = $request->checkout;
            $hotel->descripcion = $request->descripcion;
            $hotel->save();

            return back()->with('success', 'Hotel actualizado correctamente');

        } catch (ValidationException $e) {
            Log::error($e->getMessage());

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        catch (\PDOException $e) {
            Log::error($e->getMessage());
        }

    }

}
