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

    public function habitacionReserva() {
        return $this->belongsTo(HabitacionReserva::class, 'habitacionReservaID');
    }

    public function servicio() {
        return $this->belongsTo(Servicio::class, 'servicioID');
    }
    
 
}
