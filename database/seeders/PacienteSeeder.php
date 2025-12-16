<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;

class PacienteSeeder extends Seeder
{
    public function run()
    {
        $pacientes = [
            [
                'rut' => '12345678-9',
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'aaroncea@gmail.com',
                'telefono' => '+56912345678',
                'fecha_nacimiento' => '1985-03-15',
                'direccion' => 'Av. Libertador 1234, Santiago',
                'observaciones' => 'Paciente regular, sin alergias conocidas',
            ],
            [
                'rut' => '23456789-0',
                'nombre' => 'María',
                'apellido' => 'González',
                'email' => 'maria.gonzalez@email.com',
                'telefono' => '+56923456789',
                'fecha_nacimiento' => '1990-07-22',
                'direccion' => 'Calle Principal 567, Providencia',
                'observaciones' => 'Alérgica a la penicilina',
            ],
            [
                'rut' => '34567890-1',
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'email' => 'carlos.rodriguez@email.com',
                'telefono' => '+56934567890',
                'fecha_nacimiento' => '1978-11-30',
                'direccion' => 'Pasaje Los Álamos 890, Las Condes',
                'observaciones' => 'Tratamiento de ortodoncia en curso',
            ],
            [
                'rut' => '45678901-2',
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'email' => 'ana.martinez@email.com',
                'telefono' => '+56945678901',
                'fecha_nacimiento' => '1995-02-14',
                'direccion' => 'Av. Apoquindo 2345, Las Condes',
                'observaciones' => null,
            ],
            [
                'rut' => '56789012-3',
                'nombre' => 'Pedro',
                'apellido' => 'Sánchez',
                'email' => 'pedro.sanchez@email.com',
                'telefono' => '+56956789012',
                'fecha_nacimiento' => '1982-09-05',
                'direccion' => 'Calle Nueva 456, Ñuñoa',
                'observaciones' => 'Hipertensión controlada',
            ],
            [
                'rut' => '67890123-4',
                'nombre' => 'Laura',
                'apellido' => 'Torres',
                'email' => 'laura.torres@email.com',
                'telefono' => '+56967890123',
                'fecha_nacimiento' => '1988-12-18',
                'direccion' => 'Av. Vicuña Mackenna 3456, La Florida',
                'observaciones' => 'Embarazada (considerar tratamientos)',
            ],
            [
                'rut' => '78901234-5',
                'nombre' => 'Diego',
                'apellido' => 'Fernández',
                'email' => 'diego.fernandez@email.com',
                'telefono' => '+56978901234',
                'fecha_nacimiento' => '2000-04-25',
                'direccion' => 'Calle Los Espinos 789, Maipú',
                'observaciones' => 'Primera visita dental',
            ],
            [
                'rut' => '89012345-6',
                'nombre' => 'Sofía',
                'apellido' => 'López',
                'email' => 'sofia.lopez@email.com',
                'telefono' => '+56989012345',
                'fecha_nacimiento' => '1992-06-08',
                'direccion' => 'Pasaje San Martín 123, Vitacura',
                'observaciones' => 'Sensibilidad dental',
            ],
            [
                'rut' => '90123456-7',
                'nombre' => 'Andrés',
                'apellido' => 'Muñoz',
                'email' => 'andres.munoz@email.com',
                'telefono' => '+56990123456',
                'fecha_nacimiento' => '1975-01-20',
                'direccion' => 'Av. Las Condes 4567, Las Condes',
                'observaciones' => 'Fumador, limpieza cada 6 meses',
            ],
            [
                'rut' => '10234567-8',
                'nombre' => 'Valentina',
                'apellido' => 'Ramírez',
                'email' => 'valentina.ramirez@email.com',
                'telefono' => '+56910234567',
                'fecha_nacimiento' => '1998-08-12',
                'direccion' => 'Calle del Sol 678, Peñalolén',
                'observaciones' => null,
            ],
        ];

        foreach ($pacientes as $paciente) {
            Paciente::create($paciente);
        }
    }
}