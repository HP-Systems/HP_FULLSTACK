<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'huespedID',
        'fecha_entrada',
        'fecha_salida',
        'hora_entrada',
        'hora_salida',
        'status',
    ];

    public function huesped()
    {
        return $this->belongsTo(Huesped::class, 'huespedID');
    }

    public function habitaciones()
    {
        return $this->belongsToMany(Habitacion::class, 'habitaciones_reservas', 'reservaID', 'habitacionID');
    }
    
    public function tarjetas()
    {
        return $this->belongsToMany(Tarjeta::class, 'tarjetas_reservas', 'reservaID', 'tarjetaID');
    }
}
