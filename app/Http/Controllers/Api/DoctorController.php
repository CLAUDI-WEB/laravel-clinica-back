<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller\Api;
use App\Models\Doctor;
use App\Models\Horario;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::get();
        return response()->json($doctors);
    }

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'rut' => 'required|unique:doctors',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:doctors',
            'telefono' => 'required',
            'especialidad' => 'required',
            'numero_registro' => 'required|unique:doctors',
        ], [
            // Mensajes personalizados en español
            'rut.unique' => 'El RUT ya está registrado',
            'email.unique' => 'El email ya está registrado',
            'numero_registro.unique' => 'El número de registro ya está registrado',
            'email.email' => 'El formato del email no es válido',
            'required' => 'El campo :attribute es obligatorio',
        ]);

        $doctor = Doctor::create($validated);
        return response()->json($doctor, 201);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
    }
}

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        return response()->json($doctor);
    }

    public function update(Request $request, $id)
{
    try {
        $doctor = Doctor::findOrFail($id);
        
        $validated = $request->validate([
            'rut' => 'required|unique:doctors,rut,' . $id,
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:doctors,email,' . $id,
            'telefono' => 'required',
            'especialidad' => 'required',
            'numero_registro' => 'required|unique:doctors,numero_registro,' . $id,
            'activo' => 'boolean',
        ], [
            'rut.unique' => 'El RUT ya está registrado',
            'email.unique' => 'El email ya está registrado',
            'numero_registro.unique' => 'El número de registro ya está registrado',
            'email.email' => 'El formato del email no es válido',
            'required' => 'El campo :attribute es obligatorio',
        ]);
        
        $doctor->update($validated);
        return response()->json($doctor);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
    }
}

   public function horariosAgregar(Request $request) // ✅ CORREGIDO: agregado Request $request, eliminado $id
    {
        try {
            $validated = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'fecha' => 'required|date|after_or_equal:today',
                'hora' => 'required|date_format:H:i',
                'duracion' => 'required|integer|min:15|max:120',
                'especialidad' => 'nullable|string|max:255',
            ], [
                'doctor_id.required' => 'Debe seleccionar un doctor',
                'doctor_id.exists' => 'El doctor seleccionado no existe',
                'fecha.required' => 'La fecha es obligatoria',
                'fecha.after_or_equal' => 'No se pueden crear horarios en fechas pasadas',
                'hora.required' => 'La hora es obligatoria',
                'hora.date_format' => 'El formato de hora debe ser HH:MM',
                'duracion.required' => 'La duración es obligatoria',
                'duracion.min' => 'La duración mínima es 15 minutos',
                'duracion.max' => 'La duración máxima es 120 minutos',
            ]);

            // Agregar segundos a la hora (formato H:i:s)
            $horaCompleta = $validated['hora'] . ':00';

            // Verificar duplicados
            $existe = Horario::where('doctor_id', $validated['doctor_id'])
                            ->where('fecha', $validated['fecha'])
                            ->where('hora', $horaCompleta)
                            ->exists();

            if ($existe) {
                return response()->json([
                    'message' => 'Ya existe un horario para este doctor en esta fecha y hora',
                    'errors' => ['hora' => ['Este horario ya está registrado']]
                ], 422);
            }

            // Obtener datos del doctor
            $doctor = Doctor::findOrFail($validated['doctor_id']);

            // Crear el horario
            $horario = Horario::create([
                'fecha' => $validated['fecha'],
                'hora' => $horaCompleta,
                'doctor_id' => $validated['doctor_id'],
                'doctor_nombre' => $doctor->nombre,
                'especialidad' => $validated['especialidad'] ?? $doctor->especialidad,
                'disponible' => 1,
                'duracion' => $validated['duracion'],
                'paciente_id' => null,
                'observaciones' => null,
            ]);

            return response()->json([
                'message' => 'Horario creado exitosamente',
                'horario' => $horario
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el horario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function horariosDisponibles(Request $request)
    {
        try {
            $validated = $request->validate([
                'doctor_id' => 'nullable|exists:doctors,id',
                'fecha' => 'nullable|date',
            ], [
                'doctor_id.exists' => 'El doctor seleccionado no existe',
                'fecha.date' => 'El formato de fecha no es válido',
            ]);

            // Query base
            $query = Horario::query();

            // Filtrar por doctor si se proporciona
            if ($request->has('doctor_id')) {
                $query->where('doctor_id', $validated['doctor_id']);
            }

            // Filtrar por fecha si se proporciona
            if ($request->has('fecha')) {
                $query->whereDate('fecha', $validated['fecha']);
            }

            // Ordenar por hora
            $horarios = $query->orderBy('fecha', 'asc')
                             ->orderBy('hora', 'asc')
                             ->get();

            // Formatear la respuesta
            $horariosFormateados = $horarios->map(function($horario) {
                return [
                    'id' => $horario->id,
                    'fecha' => $horario->fecha->format('Y-m-d'),
                    'hora' => Carbon::parse($horario->hora)->format('H:i:s'),
                    'duracion' => $horario->duracion,
                    'disponible' => $horario->disponible,
                    'ocupado' => !$horario->disponible, // Para compatibilidad con tu frontend
                    'doctor_id' => $horario->doctor_id,
                    'doctor_nombre' => $horario->doctor_nombre,
                    'especialidad' => $horario->especialidad,
                    'paciente_id' => $horario->paciente_id,
                    'observaciones' => $horario->observaciones,
                    'paciente' => $horario->paciente ? [
                        'id' => $horario->paciente->id,
                        'nombre' => $horario->paciente->nombre . ' ' . $horario->paciente->apellido,
                    ] : null
                ];
            });

            return response()->json($horariosFormateados);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener horarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}