<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    use HasFactory;

    protected $table = 'tarjetas'; 

    protected $fillable = [
        'tipoID',
        'status',
    ];

    
    public function tipoTarjeta()
    {
        return $this->belongsTo(TipoTarjeta::class, 'tipoID');
    }

    public function limpiezas()
    {
        return $this->hasMany(Limpieza::class, 'tarjetaID');
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'tarjetas_reservas', 'tarjetaID', 'reservaID');
    }
}
