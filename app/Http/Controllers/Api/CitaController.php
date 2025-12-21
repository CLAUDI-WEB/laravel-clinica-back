<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller\Api;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Horario;
use App\Models\Doctor;
use App\Models\Cita;   
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    /**
     * Obtener las semanas de un mes específico
     */
    public function index(Request $request)
    {
        $año = $request->input('año', date('Y'));
        $mes = $request->input('mes', date('n'));
        
        $semanas = $this->obtenerSemanasDelMes($año, $mes);
        
        return response()->json([
            'año' => $año,
            'mes' => $mes,
            'nombre_mes' => Carbon::create($año, $mes, 1)->locale('es')->monthName,
            'semanas' => $semanas
        ]);
    }
    
    /**
     * Generar las semanas del mes con sus días (LUNES a DOMINGO)
     */
    private function obtenerSemanasDelMes($año, $mes)
    {
        $semanas = [];
        
        $primerDia = Carbon::create($año, $mes, 1);
        $ultimoDia = Carbon::create($año, $mes, 1)->endOfMonth();
        
        $fechaActual = $primerDia->copy()->startOfWeek();
        
        $numeroSemana = 1;
        
        while ($fechaActual->lte($ultimoDia) || $fechaActual->copy()->endOfWeek()->month == $mes) {
            $semana = [
                'numero' => $numeroSemana,
                'dias' => []
            ];
            
            for ($i = 0; $i < 7; $i++) {
                if ($fechaActual->month == $mes) {
                    $semana['dias'][] = [
                        'fecha' => $fechaActual->format('Y-m-d'),
                        'dia' => $fechaActual->day,
                        'dia_semana' => $fechaActual->locale('es')->dayName,
                        'dia_semana_corto' => $fechaActual->locale('es')->shortDayName,
                        'es_hoy' => $fechaActual->isToday(),
                        'es_fin_semana' => $fechaActual->isWeekend()
                    ];
                }
                
                $fechaActual->addDay();
            }
            
            if (!empty($semana['dias'])) {
                $semana['fecha_inicio'] = $semana['dias'][0]['fecha'];
                $semana['fecha_fin'] = end($semana['dias'])['fecha'];
                $semana['label'] = "Semana {$numeroSemana}: " . 
                    Carbon::parse($semana['fecha_inicio'])->format('d/m') . 
                    ' - ' . 
                    Carbon::parse($semana['fecha_fin'])->format('d/m');
                
                $semanas[] = $semana;
                $numeroSemana++;
            }
        }
        
        return $semanas;
    }
    
    /**
     * Obtener todas las semanas del año
     */
    public function semanasDelAño(Request $request)
    {
        $año = $request->input('año', date('Y'));
        
        $meses = [];
        
        for ($mes = 1; $mes <= 12; $mes++) {
            $meses[] = [
                'mes' => $mes,
                'nombre' => Carbon::create($año, $mes, 1)->locale('es')->monthName,
                'semanas' => $this->obtenerSemanasDelMes($año, $mes)
            ];
        }
        
        return response()->json([
            'año' => $año,
            'meses' => $meses
        ]);
    }

    /**
     *  Obtener horarios disponibles AGRUPADOS POR DOCTOR
     */
    public function horariosDisponibles(Request $request)
{
    $fecha = $request->input('fecha'); // "2025-12-16"
    
    //  LOG: Verificar fecha recibida
    // \Log::info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    // \Log::info(' HORARIOS DISPONIBLES - Fecha recibida: ' . $fecha);
    
    // Obtener todos los horarios disponibles del día con su doctor usar MODELO relacionar modelos
    $horarios = Horario::with('doctor')
        ->porFecha($fecha)
        // ->disponibles()
        ->orderBy('hora', 'asc')
        ->get();
    
    //  LOG: Verificar cuántos horarios se encontraron
    // \Log::info('Total de horarios encontrados: ' . $horarios->count());
    
    // if ($horarios->count() > 0) {
    //     \Log::info('✅ Primer horario:', [
    //         'fecha' => $horarios->first()->fecha,
    //         'hora' => $horarios->first()->hora,
    //         'doctor' => $horarios->first()->doctor->nombre ?? 'N/A'
    //     ]);
    // }
    
    // Agrupar horarios por doctor
    $doctoresConHorarios = $horarios->groupBy('doctor_id')->map(function($horariosDoctor) {
        $doctor = $horariosDoctor->first()->doctor;
        
        // Calcular rango de horarios
        $primeraHora = Carbon::parse($horariosDoctor->first()->hora);
        $ultimaHora = Carbon::parse($horariosDoctor->last()->hora);
        $horaFin = $ultimaHora->copy()->addMinutes($horariosDoctor->last()->duracion);
        
        return [
            'doctor_id' => $doctor->id,
            'doctor_nombre' => $doctor->nombre,
            'especialidad' => $doctor->especialidad,
            'foto' => $doctor->foto ?? null,
            
            'hora_inicio' => $primeraHora->format('H:i'),
            'hora_fin' => $horaFin->format('H:i'),
            'rango_horario' => $primeraHora->format('H:i') . ' - ' . $horaFin->format('H:i'),
            'total_bloques' => $horariosDoctor->count(),
            'duracion_total_minutos' => $horariosDoctor->sum('duracion'),
            
            'bloques' => $horariosDoctor->map(function($horario) {
                $horaInicio = Carbon::parse($horario->hora);
                $horaFin = $horaInicio->copy()->addMinutes($horario->duracion);
                
                return [
                    'id' => $horario->id,
                    'hora_inicio' => $horaInicio->format('H:i'),
                    'hora_fin' => $horaFin->format('H:i'),
                    'hora_completa' => $horario->hora,
                    'duracion' => $horario->duracion,
                    'disponible' => $horario->disponible
                ];
            })->values()->toArray()
        ];
    })->values();
    
    //  LOG: Verificar resultado final
    // \Log::info('📦 Resultado agrupado:', [
    //     'total_doctores' => $doctoresConHorarios->count(),
    //     'total_bloques' => $horarios->count()
    // ]);
    // \Log::info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    
    return response()->json([
        'fecha' => $fecha,
        'total_doctores' => $doctoresConHorarios->count(),
        'total_bloques' => $horarios->count(),
        'doctores' => $doctoresConHorarios
    ]);
}

    /**
     * Crear/Agendar una cita
     */
  public function agendarCita(Request $request)
{
    $request->validate([
        'horario_id' => 'required|exists:horarios,id',
        'observaciones' => 'nullable|string'
    ]);

    // Obtener el paciente autenticado
    $paciente = Auth::guard('paciente')->user();
    
    if (!$paciente) {
        return response()->json([
            'message' => 'No autenticado'
        ], 401);
    }

    $horario = Horario::findOrFail($request->horario_id);

    // Verificar que esté disponible
    if (!$horario->disponible) {
        return response()->json([
            'error' => 'Este horario ya no está disponible'
        ], 409);
    }

    // Reservar el horario con el paciente autenticado
    $horario->reservar($paciente->id);

    if ($request->observaciones) {
        $horario->update(['observaciones' => $request->observaciones]);
    }

    return response()->json([
        'message' => 'Cita agendada exitosamente',
        'horario' => $horario->load('doctor', 'paciente')
    ]);
}

    /**
     * Cancelar una cita
     */
    public function cancelarCita($horarioId)
    {
        $horario = Horario::findOrFail($horarioId);

        // Liberar el horario
        $horario->liberar();

        return response()->json([
            'message' => 'Cita cancelada exitosamente',
            'horario' => $horario->load('doctor')
        ]);
    }

     public function citasTomadas(Request $request)
    {
        \Log::info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        \Log::info('🧪 TEST: citasTomadas() llamado');
        \Log::info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        try {
            $citas = Horario::with(['doctor', 'paciente'])
                ->where('disponible', false)
                ->whereNotNull('paciente_id')
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'asc')
                ->get()
                ->map(function($horario) {
                    return [
                        'id' => $horario->id,
                        'fecha' => $horario->fecha,
                        'hora' => $horario->hora,
                        'duracion' => $horario->duracion,
                        
                        // Información del paciente
                        'paciente_id' => $horario->paciente_id,
                        'paciente_nombre' => $horario->paciente->nombre ?? 'Sin nombre',
                        'paciente_apellido' => $horario->paciente->apellido ?? 'Sin nombre',
                        'paciente_rut' => $horario->paciente->rut ?? null,
                        
                        // Información del doctor
                        'doctor_id' => $horario->doctor_id,
                        'doctor_nombre' => $horario->doctor->nombre ?? $horario->doctor_nombre,
                        'especialidad' => $horario->doctor->especialidad ?? $horario->especialidad,
                        
                        // Estado y observaciones
                        'estado' => $this->determinarEstado($horario),
                        'observaciones' => $horario->observaciones
                    ];
                });

            \Log::info('✅ Total de citas encontradas: ' . $citas->count());
            \Log::info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            return response()->json([
                'success' => true,
                'total' => $citas->count(),
                'citas' => $citas
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error en citasTomadas():');
            \Log::error('Mensaje: ' . $e->getMessage());
            \Log::error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las citas tomadas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determinar el estado de una cita según la fecha
     */
    private function determinarEstado($horario)
    {
        $fecha = \Carbon\Carbon::parse($horario->fecha);
        $hoy = \Carbon\Carbon::today();

        if ($fecha->lt($hoy)) {
            return 'completada'; // Pasada
        } elseif ($fecha->eq($hoy)) {
            return 'confirmada'; // Hoy
        } else {
            return 'pendiente'; // Futura
        }
    }
}