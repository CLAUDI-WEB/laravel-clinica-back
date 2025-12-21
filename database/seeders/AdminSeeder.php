<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Paciente::create([
            'rut' => '11111111-1',
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'email' => 'admin@clinica.com',
            'password' => Hash::make('admin123'),
            'telefono' => '999999999',
            'fecha_nacimiento' => '1990-01-01',
            'rol' => 'admin',
        ]);
    }
}