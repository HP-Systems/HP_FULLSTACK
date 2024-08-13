<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            HotelSeeder::class,
            tipo_habitacion::class,
            tipos_personal::class,
            tipos_tarjetas::class,
            tipos_servicios::class,
            AdminSeeder::class,
            habitacion::class,
        ]);
    }
}
