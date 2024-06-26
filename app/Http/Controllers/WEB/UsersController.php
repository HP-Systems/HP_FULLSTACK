<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\User;
use App\Models\Rol;

class UsersController extends Controller
{
    public function index(){
        $usuarios = DB::table('users as u')
            ->join('personal as p', function ($join) {
                $join->on('u.userable_id', '=', 'p.id')
                     ->where('u.userable_type', '=', '1'); 
            })
            ->join('roles as r', 'r.id', '=', 'p.rolID')
            ->selectRaw('u.email, u.status, CONCAT(p.nombre, " ", p.apellido) as nombre_completo, r.nombre as rol')
            ->get();

        return view('users.users', compact('usuarios'));
    }
}
