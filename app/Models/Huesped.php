<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Huesped extends Model
{
    use HasFactory;

    protected $table = 'huespedes';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'huespedID');
    }

    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    

}
