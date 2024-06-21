<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Personal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un registro en la tabla personal
        $personal = Personal::create([
            'nombre' => 'Berenice',
            'apellido' => 'de la Cruz',
            'telefono' => '8713958847',
            'rolID' => 1,
        ]);

        // Crear el usuario correspondiente
        User::create([
            'email' => 'berenicedlcrz13@gmail.com',
            'password' => Hash::make('Berenice13'), 
            'userable_id' => $personal->id,
            'userable_type' => 1, 
        ]);
    }
}
