<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTarjeta extends Model
{
    use HasFactory;

    protected $table = 'tipo_tarjeta'; 

    protected $fillable = [
        'tipo',
    ];

    public function tarjetas()
    {
        return $this->hasMany(Tarjeta::class, 'tipoID');
    }
}
