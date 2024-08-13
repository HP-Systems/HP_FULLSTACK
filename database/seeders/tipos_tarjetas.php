<?php

namespace Database\Seeders;

use App\Models\TipoTarjeta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tipos_tarjetas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoTarjeta::firstOrCreate([
            'tipo' => 'Administrativa',
        ]);
        TipoTarjeta::firstOrCreate([
            'tipo' => 'Huesped',
        ]);
        TipoTarjeta::firstOrCreate([
            'tipo' => 'Limpieza',
        ]);
    }
}
