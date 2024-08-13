<?php

namespace Database\Seeders;

use App\Models\TipoHabitacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tipo_habitacion extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Sencilla',
            'capacidad'=> '1',
            'imagen' => 'sencilla.jpg',
            'descripcion' => 'Habitación sencilla',
            'precio_noche' => '800',
        ]);
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Doble',
            'capacidad'=> '2',
            'imagen' => 'doble.jpg',
            'descripcion' => 'Habitación doble',
            'precio_noche' => '1200',
        ]);
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Presidencial',
            'capacidad'=> '5',
            'imagen' => 'presidencial.jpg',
            'descripcion' => 'Habitación presidencial',
            'precio_noche' => '2500',
        ]);
    }
}
