<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;
use App\Models\Doctor;  // 🆕 Importar modelo Doctor
use Carbon\Carbon;

class HorariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla
        Horario::truncate();

        // 🆕 Obtener doctores REALES desde la base de datos
        $doctores = Doctor::all();

        // Validar que existan doctores
        if ($doctores->isEmpty()) {
            $this->command->error('❌ No hay doctores en la base de datos!');
            $this->command->info('💡 Por favor ejecuta primero: php artisan db:seed --class=DoctoresSeeder');
            return;
        }

        $this->command->info("✅ Se encontraron {$doctores->count()} doctores");

        // Horarios de atención: 9:00 a 18:00
        $horas = [
            '09:00:00', '09:30:00',
            '10:00:00', '10:30:00',
            '11:00:00', '11:30:00',
            '12:00:00', '12:30:00',
            '14:00:00', '14:30:00', // Pausa de almuerzo de 13:00 a 14:00
            '15:00:00', '15:30:00',
            '16:00:00', '16:30:00',
            '17:00:00', '17:30:00',
        ];

        // Generar horarios para los próximos 30 días
        $fechaInicio = Carbon::now();
        $fechaFin = Carbon::now()->addDays(30);

        $fecha = $fechaInicio->copy();
        $totalCreados = 0;

        while ($fecha->lte($fechaFin)) {
            // No generar horarios para fines de semana
            if (!$fecha->isWeekend()) {
                foreach ($horas as $hora) {
                    // 🆕 Seleccionar doctor aleatorio de la base de datos
                    $doctor = $doctores->random();

                    Horario::create([
                        'fecha' => $fecha->format('Y-m-d'),
                        'hora' => $hora,
                        'doctor_id' => $doctor->id,              // 🆕 ID del doctor
                        'doctor_nombre' => $doctor->nombre,       // Mantener por compatibilidad
                        'especialidad' => $doctor->especialidad,  // Mantener por compatibilidad
                        'disponible' => true,
                        'duracion' => 30
                    ]);

                    $totalCreados++;
                }
            }

            $fecha->addDay();
        }

        $this->command->info("✅ {$totalCreados} horarios creados exitosamente!");
        
        // Mostrar resumen por doctor
        $this->command->info("\n📊 Resumen por doctor:");
        foreach ($doctores as $doctor) {
            $count = Horario::where('doctor_id', $doctor->id)->count();
            $this->command->info("   Dr. {$doctor->nombre} ({$doctor->especialidad}): {$count} horarios");
        }
    }
}