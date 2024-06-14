<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitacionReserva extends Model
{
    use HasFactory;

    protected $table = 'habicaciones_reservas'; 


    protected $fillable = [
        'reservaID',
        'habitacion_piso',
        'habitacion_numero',
    ];

   
}
