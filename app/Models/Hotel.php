<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $table = 'informacion';

    protected $fillable = [
        'nombre',
        'direccion',
        'email',
        'telefono',
        'checkin',
        'checkout',
        'descripcion'
    ];
}
