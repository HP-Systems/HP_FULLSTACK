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
            'descripcion' => 'Habitación sencilla',
            'precio_noche' => '800',
        ]);
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Doble',
            'capacidad'=> '2',
            'descripcion' => 'Habitación doble',
            'precio_noche' => '1200',
        ]);
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Triple',
            'capacidad'=> '3',
            'descripcion' => 'Habitación triple',
            'precio_noche' => '1500',
        ]);
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Suite',
            'capacidad'=> '4',
            'descripcion' => 'Habitación suite',
            'precio_noche' => '2000',
        ]);
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Presidencial',
            'capacidad'=> '5',
            'descripcion' => 'Habitación presidencial',
            'precio_noche' => '2500',
        ]);
        TipoHabitacion::firstOrCreate([
            'tipo' => 'Familiar',
            'capacidad'=> '6',
            'descripcion' => 'Habitación familiar',
            'precio_noche' => '3000',
        ]);

    }
}
