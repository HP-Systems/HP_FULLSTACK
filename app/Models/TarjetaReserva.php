<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaReserva extends Model
{
    use HasFactory;

    protected $table = 'tarjetas_reservas';
    
    protected $fillable = [
        'reservaID', 
        'tarjetaID', 
        'status'
    ];
    
}
