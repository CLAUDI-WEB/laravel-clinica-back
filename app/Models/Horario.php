<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'horarios';

    /**
     * The attributes that are mass assignable.
     */
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

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime:H:i',
        'disponible' => 'boolean',
        'duracion' => 'integer'
    ];

    /**
     * Relación con Doctor (si tienes tabla de doctores)
     * Si no la tienes, comenta o elimina este método
     */
    // public function doctor()
    // {
    //     return $this->belongsTo(Doctor::class);
    // }

    /**
     * Relación con Paciente (si tienes tabla de pacientes)
     */
    // public function paciente()
    // {
    //     return $this->belongsTo(Paciente::class);
    // }

    /**
     * Scope para obtener solo horarios disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Marcar horario como reservado
     */
    public function reservar($pacienteId = null)
    {
        $this->update([
            'disponible' => false,
            'paciente_id' => $pacienteId
        ]);
    }

    /**
     * Liberar horario
     */
    public function liberar()
    {
        $this->update([
            'disponible' => true,
            'paciente_id' => null
        ]);
    }

        public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}