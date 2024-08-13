<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tipos_personal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::firstOrCreate([
            'nombre' => 'Administrador',
        ]);
        Rol::firstOrCreate([
            'nombre' => 'Recepcionista',
        ]);	
        Rol::firstOrCreate([
            'nombre' => 'Limpieza',
        ]);
    }
}
