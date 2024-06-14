<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    use HasFactory;

    protected $table = 'tipo_servicio';

    protected $fillable = [
        'tipo'
    ];
    
    
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'tipoID');
    }

}
