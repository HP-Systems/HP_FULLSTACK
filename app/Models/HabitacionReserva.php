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

    public function habitacionesReservas() {
        return $this->hasMany(ServicioReserva::class, 'habitacionReservaID');
    }

    public function servicios() {
        return $this->hasMany(ServicioReserva::class, 'servicioID');
    }

    public function limpiezas() {
        return $this->hasMany(Limpieza::class, 'habitacion_reservaID');
    }

}
