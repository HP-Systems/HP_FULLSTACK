<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    protected $table = 'tipo_habitacion';

    protected $fillable = [
        'tipo',
        'capacidad',
        'precio_noche',
        'descripcion',
    ];

    
    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class, 'tipoID');
    }
}
