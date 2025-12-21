<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Auth\PatientAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==================== RUTAS DE AUTENTICACIÓN ====================
// Rutas públicas de autenticación (no requieren login)
Route::prefix('auth')->group(function () {
    Route::post('/login', [PatientAuthController::class, 'login']);
    Route::post('/register', [PatientAuthController::class, 'register']);
});

// Rutas protegidas de autenticación (requieren login con guard 'paciente')
Route::middleware('auth:paciente')->prefix('auth')->group(function () {
    Route::get('/user', [PatientAuthController::class, 'user']);
    Route::post('/logout', [PatientAuthController::class, 'logout']);
    Route::put('/profile', [PatientAuthController::class, 'updateProfile']);
    Route::post('/change-password', [PatientAuthController::class, 'changePassword']);
});

// ==================== RUTAS DE PRUEBA ====================
Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});

// ==================== RUTAS DE PACIENTES ====================
Route::middleware('auth:paciente')->group(function () {
    Route::get('/pacientes', [PacienteController::class, 'index']);
    Route::post('/pacientes', [PacienteController::class, 'store']);
    Route::get('/pacientes/{id}', [PacienteController::class, 'show']);
    Route::put('/pacientes/{id}', [PacienteController::class, 'update']);
    Route::delete('/pacientes/{id}', [PacienteController::class, 'destroy']);
});

// ==================== RUTAS DE DOCTORES ====================
Route::middleware('auth:paciente')->group(function () {
    Route::apiResource('doctors', DoctorController::class);
});

// ==================== RUTAS DE CITAS ====================
// Rutas públicas de citas (si necesitas que sean públicas)
Route::get('citas/semanas', [CitaController::class, 'index']);
Route::get('citas/horarios-disponibles', [CitaController::class, 'horariosDisponibles']);


// Rutas públicas de citas (si necesitas que sean públicas)
Route::get('citas/semanas', [CitaController::class, 'index']);
  Route::get('/horarios/horarios-disponibles', [DoctorController::class, 'horariosDisponibles']);
Route::post('/horarios', [DoctorController::class, 'horariosAgregar']);

// Rutas protegidas de citas
Route::middleware('auth:paciente')->group(function () {
    Route::post('citas/agendar', [CitaController::class, 'agendarCita']);
    Route::post('citas/cancelar/{horarioId}', [CitaController::class, 'cancelarCita']);
    Route::get('citas/tomadas', [CitaController::class, 'citasTomadas']);
});