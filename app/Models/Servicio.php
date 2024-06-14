<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'precio', 
        'tipoID'
    ];
    

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'tipoID');
    }
    

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'servicios_reservas', 'servicioID', 'reservaID');
    }
}
