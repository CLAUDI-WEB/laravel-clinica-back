<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\CitaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});

//rutas automaticas tipo resource
// Route::apiResource('pacientes', PacienteController::class);

Route::get('/pacientes', [PacienteController::class, 'index']);
Route::post('/pacientes', [PacienteController::class, 'store']);
Route::get('/pacientes/{id}', [PacienteController::class, 'show']);
Route::put('/pacientes/{id}', [PacienteController::class, 'update']);
Route::delete('/pacientes/{id}', [PacienteController::class, 'destroy']);


Route::apiResource('doctors', DoctorController::class);


//  Route::apiResource('citas', CitaController::class);

Route::get('citas/semanas', [CitaController::class, 'index']);
    // Route::get('citas/semanas/año', [CitaController::class, 'semanasDelAño']);

Route::get('citas/horarios-disponibles', [CitaController::class, 'horariosDisponibles']);
Route::post('citas/agendar', [CitaController::class, 'agendarCita']);
Route::post('citas/cancelar/{horarioId}', [CitaController::class, 'cancelarCita']);