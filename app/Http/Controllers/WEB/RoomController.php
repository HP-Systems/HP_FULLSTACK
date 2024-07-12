<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function index()
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
        $tipoHabitaciones = TipoHabitacion::all();
        return view('habitaciones.habitaciones', ['habitaciones' => $habitaciones, 'tipoHabitaciones' => $tipoHabitaciones]);
    }

    public function storeTipo(Request $request)
    {
        try {
            //diccionario de errores
            $messages = [
                'required' => 'El campo :attribute es obligatorio',
                'string' => 'El campo :attribute debe ser un texto',
                'numeric' => 'El campo :attribute debe ser un número',
            ];

            $validator = Validator::make($request->all(), [
                'tipo' => 'required|string',
                'capacidad' => 'required|numeric',
                'precio_noche' => 'required|numeric',
                'descripcion' => 'required|string',
            ], $messages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $tipo = new TipoHabitacion();
            $tipo->tipo = $request->tipo;
            $tipo->capacidad = $request->capacidad;
            $tipo->precio_noche = $request->precio_noche;
            $tipo->descripcion = $request->descripcion;
            $tipo->save();
            return response()->json($tipo, 200);
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        } catch (\PDOException $e) {
            Log::error($e->getMessage());
        }
    }

    public function updateTipo(Request $request, $id)
    {
        try {
            // Buscar el tipo de habitación por ID
            $tipo = TipoHabitacion::find($id);
            if ($tipo == null) {
                return response()->json(['error' => 'Tipo de habitación no encontrado'], 404);
            }

            // Diccionario de errores
            $messages = [
                'required' => 'El campo :attribute es obligatorio',
                'string' => 'El campo :attribute debe ser un texto',
                'numeric' => 'El campo :attribute debe ser un número',
            ];

            $validator = Validator::make($request->all(), [
                'tipo' => 'required|string',
                'capacidad' => 'required|numeric',
                'precio_noche' => 'required|numeric',
                'descripcion' => 'required|string',
            ], $messages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $tipo->tipo = $request->tipo;
            $tipo->capacidad = $request->capacidad;
            $tipo->precio_noche = $request->precio_noche;
            $tipo->descripcion = $request->descripcion;
            $tipo->save();
            return response()->json($tipo, 200);
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        } catch (\PDOException $e) {
            Log::error($e->getMessage());
        }
    }
    public function storeRoom(Request $request)
    {
        try {

            $messages = [
                'required' => 'El campo :attribute es obligatorio',
                'string' => 'El campo :attribute debe ser un texto',
                'numeric' => 'El campo :attribute debe ser un número',
                'imagen' => 'El campo :attribute debe ser una imagen',
                'numero.unique' => 'El número de habitación ya esta en uso',
            ];
            $validator = Validator::make($request->all(), [
                'numero' => 'required|numeric|unique:habitaciones,numero',
                'tipoID' => 'required|numeric',
                'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if (!TipoHabitacion::find($request->tipoID)) {
                return back()->with('error', 'Tipo de habitación no encontrado');
            }
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();
                $destino = public_path('/images');
                $imagen->move($destino, $nombreImagen);
                $habitacion = new Habitacion();
                $habitacion->numero = $request->numero;
                $habitacion->tipoID = $request->tipoID;
                $habitacion->status = $request->status;
                $habitacion->imagen = $nombreImagen;
                $habitacion->save();
                return back()->with('success', 'Habitación actualizada con éxito');
            }
            return back ()->withErrors($validator)->withInput();
           
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        } catch (\PDOException $e) {
            Log::error($e->getMessage());
        }
    }
    public function updateRoom(Request $request, $id)
    {
        try {
            $habitacion = Habitacion::find($id);
            if ($habitacion == null) {
                return back()->with('error', 'Habitación no encontrada');
            }

            $messages = [
                'required' => 'El campo :attribute es obligatorio',
                'string' => 'El campo :attribute debe ser un texto',
                'numeric' => 'El campo :attribute debe ser un número',
                'imagen' => 'El campo :attribute debe ser una imagen',
                'status' => 'El campo :attribute debe ser un booleano',
                'numero.unique' => 'El número de habitación ya esta en uso',
            ];
            $validator = Validator::make($request->all(), [
                'numero' => 'required|numeric|unique:habitaciones,numero,' . $id . ',id',
                'tipoID' => 'required|numeric',
                'imagen' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|boolean',

            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if (!TipoHabitacion::find($request->tipoID)) {
                $validator->errors()->add('tipoID', 'Tipo de habitación no encontrado');
                return back()->withErrors($validator)->withInput();
            }
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();
                $destino = public_path('/images');
                $imagen->move($destino, $nombreImagen);

                $habitacion->numero = $request->numero;
                $habitacion->tipoID = $request->tipoID;
                $habitacion->status = $request->status;
                $habitacion->imagen = $nombreImagen;
                $habitacion->save();
                return back()->with('success', 'Habitación actualizada con éxito');
            }

            //si no se selecciona una imagen guardamos la imagen que ya tenia 
            $habitacion->numero = $request->numero;
            $habitacion->tipoID = $request->tipoID;
            $habitacion->status = $request->status;
            $habitacion->save();
            return back()->with('success', 'Habitación actualizada con éxito');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\PDOException $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function buscar(Request $request)
    {
        $numero = $request->input('numero');
    
        if (empty($numero)) {
            return response()->json(['html' => '']);
        }
    
        $habitaciones = Habitacion::where('numero', 'like', '%' . $numero . '%')->get();
        $habitaciones->map(function ($habitacion) {
            $habitacion->tipo = $habitacion->tipoHabitacion->tipo;
            $habitacion->capacidad = $habitacion->tipoHabitacion->capacidad;
            $habitacion->precio_noche = $habitacion->tipoHabitacion->precio_noche;
            $habitacion->descripcion = $habitacion->tipoHabitacion->descripcion;
            unset($habitacion->tipoHabitacion);
            return $habitacion;
        });
        $tipoHabitaciones = TipoHabitacion::all();
    
        $view = view('habitaciones.roomResults', ['habitaciones' => $habitaciones, 'tipoHabitaciones' => $tipoHabitaciones])->render();
    
        return response()->json(['html' => $view]);
    }

    public function indexTipos(){
        $tipos = TipoHabitacion::all();

        return view('habitaciones.tipos_habitaciones', compact('tipos'));
    }
    
    public function insertTipoHabitacion(Request $request){
        try{
            $validation = Validator::make(
                $request->all(), 
                [
                    'tipo_habitacion' => 'required',
                    'descripcion' => 'required',
                    'precio' => 'required',
                    'capacidad' => 'required',
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $tipo_habitacion = TipoHabitacion::create([
                'tipo' => $request->tipo_habitacion,
                'descripcion' => $request->descripcion,
                'precio_noche' => $request->precio,
                'capacidad' => $request->capacidad
            ]);

            return response()->json(['msg' => 'El tipo de habitacion ha sido creado correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during insertTipoHabitacion: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al crear el tipo de habitacion.']);
        }
    }

    public function editTipoHabitacion(Request $request){
        try{
            $validation = Validator::make(
                $request->all(), 
                [
                    'tipo_habitacion' => 'required',
                    'descripcion' => 'required',
                    'precio' => 'required',
                    'capacidad' => 'required',
                ]
            );

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()->all()]);
            }

            $tipo_habitacion = TipoHabitacion::find($request->id);
            $tipo_habitacion->tipo = $request->tipo_habitacion;
            $tipo_habitacion->descripcion = $request->descripcion;
            $tipo_habitacion->precio_noche = $request->precio;
            $tipo_habitacion->capacidad = $request->capacidad;
            $tipo_habitacion->save();

            return response()->json(['edit' => 'El tipo de habitación ha sido editado correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Exception during editTipoHabitacion: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al editar el tipo de habitación.']);
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

            $tipo_habitacion = TipoHabitacion::find($request->id);
            $tipo_habitacion->status = $request->status;
            
            if ($tipo_habitacion->save()) {
                Habitacion::where('tipoID', $request->id)->update(['status' => $request->status]);
    
                return response()->json(['msg' => 'Tipo de habitación actualizado con éxito'], 200);
            } else {
                return response()->json(['error' => 'No se pudo actualizar el tipo de habitación.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception during cambiarStatusTipoServicio: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el tipo de habitación.']);
        }
    }
    
}
