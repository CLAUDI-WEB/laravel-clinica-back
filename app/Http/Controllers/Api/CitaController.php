<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller\Api;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Horario;

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
     * Obtener horarios disponibles de un día específico
     */
    public function horariosDisponibles(Request $request)
    {
        $fecha = $request->input('fecha'); // "2025-12-08"
        
        // Obtener horarios disponibles del día ordenados por hora
        $horarios = Horario::porFecha($fecha)
            ->disponibles()
            ->orderBy('hora', 'asc')
            ->get()
            ->map(function($horario) {
                return [
                    'id' => $horario->id,
                    'hora' => $horario->hora,
                    'doctor_nombre' => $horario->doctor_nombre,
                    'especialidad' => $horario->especialidad,
                    'duracion' => $horario->duracion,
                    'disponible' => $horario->disponible
                ];
            });
        
        return response()->json([
            'horarios' => $horarios,
            'fecha' => $fecha,
            'total' => $horarios->count()
        ]);
    }

    /**
     * Crear/Agendar una cita
     */
    public function agendarCita(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'paciente_id' => 'nullable|integer',
            'observaciones' => 'nullable|string'
        ]);

        $horario = Horario::findOrFail($request->horario_id);

        // Verificar que esté disponible
        if (!$horario->disponible) {
            return response()->json([
                'error' => 'Este horario ya no está disponible'
            ], 409);
        }

        // Reservar el horario
        $horario->reservar($request->paciente_id);

        if ($request->observaciones) {
            $horario->update(['observaciones' => $request->observaciones]);
        }

        return response()->json([
            'message' => 'Cita agendada exitosamente',
            'horario' => $horario
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
            'horario' => $horario
        ]);
    }
}