<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call([
            PacienteSeeder::class,  // ← Esta línea debe estar
            DoctorSeeder::class,
            HorariosSeeder::class,
        ]);
    }
}
