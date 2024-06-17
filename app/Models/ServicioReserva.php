<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioReserva extends Model
{
    use HasFactory;

    protected $table = 'servicios_reservas';

    protected $fillable = [
        'servicioID', 
        'reservaID', 
        'cantidad'
    ];
    
 
}
