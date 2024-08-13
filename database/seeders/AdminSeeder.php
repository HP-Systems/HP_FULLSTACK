<?php

namespace Database\Seeders;

use App\Mail\passwPersonalMail;
use App\Models\User;
use App\Models\Personal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Mail;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un registro en la tabla personal
        $personal = Personal::firstOrCreate([
            'nombre' => 'Berenice',
            'apellido' => 'de la Cruz',
            'telefono' => '8713958847',
            'rolID' => 1,
        ]);
        $user= User::firstOrCreate([
            'email' => 'berenicedlcrz13@gmail.com',
            'password' => Hash::make('Berenice13'), 
            'userable_id' => $personal->id,
            'userable_type' => 1, 
        ]);
        Mail::to($user->email)->send(new passwPersonalMail($user, 'Berenice13'));


        $personal=Personal::firstOrCreate([
            'nombre' => 'Nadia',
            'apellido' => 'Salazar',
            'telefono' => '8715854676',
            'rolID' => 1,
        ]);
        $user=User::firstOrCreate([
            'email' => 'nadiasalzr@gmail.com',
            'password' => Hash::make('nadianadia'), 
            'userable_id' => $personal->id,
            'userable_type' => 1, 
            
        ]);
        Mail::to($user->email)->send(new passwPersonalMail($user, 'nadianadia'));


        $personal=Personal::firstOrCreate([
            'nombre' => 'Cristian',
            'apellido' => 'Avitia',
            'telefono' => '8715854676',
            'rolID' => 2,
        ]);
        $user=User::firstOrCreate([
            'email' => 'crismoo3012@gmail.com',
            'password' => Hash::make('QUw£N/3//*X^X43"J'), 
            'userable_id' => $personal->id,
            'userable_type' => 1, 
        ]);
        Mail::to($user->email)->send(new passwPersonalMail($user, 'QUw£N/3//*X^X43"J'));

    }
}
