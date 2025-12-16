<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        $doctors = [
            [
                'rut' => '11111111-1',
                'nombre' => 'dr.Roberto',
                'apellido' => 'Silva',
                'email' => 'roberto.silva@dental.cl',
                'telefono' => '+56911111111',
                'especialidad' => 'Odontología General',
                'numero_registro' => 'REG-001',
                'activo' => true,
            ],
            [
                'rut' => '22222222-2',
                'nombre' => 'dr.Carmen',
                'apellido' => 'Vargas',
                'email' => 'carmen.vargas@dental.cl',
                'telefono' => '+56922222222',
                'especialidad' => 'Ortodoncia',
                'numero_registro' => 'REG-002',
                'activo' => true,
            ],
            [
                'rut' => '33333333-3',
                'nombre' => 'dr.Felipe',
                'apellido' => 'Morales',
                'email' => 'felipe.morales@dental.cl',
                'telefono' => '+56933333333',
                'especialidad' => 'Endodoncia',
                'numero_registro' => 'REG-003',
                'activo' => true,
            ],
            [
                'rut' => '44444444-4',
                'nombre' => 'dra.Patricia',
                'apellido' => 'Rojas',
                'email' => 'patricia.rojas@dental.cl',
                'telefono' => '+56944444444',
                'especialidad' => 'Periodoncia',
                'numero_registro' => 'REG-004',
                'activo' => true,
            ],
            [
                'rut' => '55555555-5',
                'nombre' => 'dr.Miguel',
                'apellido' => 'Castro',
                'email' => 'miguel.castro@dental.cl',
                'telefono' => '+56955555555',
                'especialidad' => 'Cirugía Oral',
                'numero_registro' => 'REG-005',
                'activo' => true,
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }
    }
}