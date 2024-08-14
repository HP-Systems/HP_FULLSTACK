<?php

namespace Database\Seeders;

use App\Models\Habitacion as ModelsHabitacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class habitacion extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsHabitacion::firstOrCreate([
            'numero' => '1',
            'tipoID' => '1',
            'imagen' => 'imagen.jpg',
        ]);
        ModelsHabitacion::firstOrCreate([
            'numero' => '2',
            'tipoID' => '2',
            'imagen' => 'imagen.jpg'
        ]);
        ModelsHabitacion::firstOrCreate([
            'numero' => '3',
            'tipoID' => '3',
            'imagen' => 'imagen.jpg'
        ]);
    }
}
