<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Limpieza extends Model
{
    use HasFactory;
    protected $table = 'limpiezas'; 

    protected $fillable = [
        'personalID',
        'habitacion_reservaID',
        'tarjetaID',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'status',
    ];

    
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'personalID');
    }

    
    /*public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reservaID');
    } */

    public function habitacion_reserva()
    {
        return $this->belongsTo(HabitacionReserva::class, 'habitacion_reservaID');
    }

    
    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class, 'tarjetaID');
    }
}