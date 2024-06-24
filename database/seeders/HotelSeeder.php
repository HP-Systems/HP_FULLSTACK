<?php

namespace Database\Seeders;

use App\Models\Hotel;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hotel::firstOrCreate([
            'nombre' => 'Hotel',
            'direccion' => 'Calle 123',
            'email' => 'hotel@gmail.com',
            'telefono' => '1234567890',
            'checkin' => '12:00',
            'checkout' => '12:00',
            'descripcion' => 'Hotel de prueba',
        ]);

    }
}
