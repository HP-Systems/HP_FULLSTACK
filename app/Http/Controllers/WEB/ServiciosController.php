<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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

        $tipos = TipoServicio::all();

        return view('servicios.servicios', compact('servicios', 'tipos'));
    }
}
