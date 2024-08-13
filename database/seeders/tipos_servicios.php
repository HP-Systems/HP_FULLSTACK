<?php

namespace Database\Seeders;

use App\Models\TipoServicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tipos_servicios extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoServicio::firstOrCreate([
            'tipo' => 'Servicio de habitación',
        ]);
        TipoServicio::firstOrCreate([
            'tipo' => 'Transporte',
        ]);
        TipoServicio::firstOrCreate([
            'tipo' => 'Limpieza',
        ]);
    }
}
