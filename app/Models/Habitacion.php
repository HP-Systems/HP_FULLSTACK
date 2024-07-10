<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones'; 


    protected $fillable = [
        'numero',
        'tipoID',
        'imagen',
    ];
    

    public function tipoHabitacion()
    {
        return $this->belongsTo(TipoHabitacion::class, 'tipoID');
    }

    public function limpiezas()
    {
        return $this->hasMany(Limpieza::class, 'habitacionID');
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'habitaciones_reservas', 'habitacionID', 'reservaID');
    }

}