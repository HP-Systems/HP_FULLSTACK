<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'personal'; 

    protected $fillable = [
        'apellido',
        'telefono',
        'rolID',
    ];

    
    public function role()
    {
        return $this->belongsTo(Rol::class, 'rolID');
    }

    
    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    
    public function limpiezas()
    {
        return $this->hasMany(Limpieza::class, 'personalID');
    }
}
