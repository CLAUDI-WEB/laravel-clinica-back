<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';

    protected $fillable = [
        'fecha',
        'hora',
        'doctor_id',
        'doctor_nombre',
        'especialidad',
        'disponible',
        'duracion',
        'paciente_id',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'disponible' => 'boolean',
        'duracion' => 'integer',
    ];

    // ══════════════════════════════════════════════════════════════
    // RELACIONES
    // ══════════════════════════════════════════════════════════════

    /**
     * Un horario pertenece a un doctor
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Un horario puede tener un paciente asignado
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    // ══════════════════════════════════════════════════════════════
    // SCOPES - Filtros reutilizables
    // ══════════════════════════════════════════════════════════════

    /**
     * Scope: Filtrar por fecha específica
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    /**
     * Scope: Solo horarios disponibles en el codigo lo deje como 0 o 1 booleano
     */
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    /**
     * Scope: Solo horarios ocupados
     */
    public function scopeOcupados($query)
    {
        return $query->where('disponible', false);
    }

    /**
     * Scope: Filtrar por doctor
     */
    public function scopePorDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope: Filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    // ══════════════════════════════════════════════════════════════
    // MÉTODOS DE NEGOCIO
    // ══════════════════════════════════════════════════════════════

    /**
     * Reservar el horario para un paciente
     */
    public function reservar($pacienteId = null)
    {
              \Log::info('✅ Total de citas pacienteId: ' . $pacienteId);
        $this->update([
            'disponible' => false,
            'paciente_id' => $pacienteId
        ]);

        return $this;
    }

    /**
     * Liberar el horario
     */
    public function liberar()
    {
        $this->update([
            'disponible' => true,
            'paciente_id' => null,
            'observaciones' => null
        ]);

        return $this;
    }

    /**
     * Verificar si el horario está disponible
     */
    public function estaDisponible()
    {
        return $this->disponible === true;
    }

    // ══════════════════════════════════════════════════════════════
    // ACCESSORS - Atributos calculados
    // ══════════════════════════════════════════════════════════════

    /**
     * Obtener hora de inicio formateada
     */
    public function getHoraInicioFormateadaAttribute()
    {
        return Carbon::parse($this->hora)->format('H:i');
    }

    /**
     * Calcular hora de fin basada en duración
     */
    public function getHoraFinAttribute()
    {
        return Carbon::parse($this->hora)
            ->addMinutes($this->duracion)
            ->format('H:i');
    }

    /**
     * Obtener rango completo de hora
     */
    public function getRangoHoraAttribute()
    {
        return "{$this->hora_inicio_formateada} - {$this->hora_fin}";
    }
}