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
        'habitacionID',
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

    
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacionID');
    }

    
    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class, 'tarjetaID');
    }
}