<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Paciente;

class PatientAuthController extends Controller
{
    /**
     * Login de paciente
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Intentar autenticar usando el guard 'paciente'
        if (Auth::guard('paciente')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            
            $paciente = Auth::guard('paciente')->user();
            
            return response()->json([
                'user' => $paciente,
                'message' => 'Login exitoso'
            ], 200);
        }

        return response()->json([
            'message' => 'Las credenciales proporcionadas son incorrectas'
        ], 401);
    }

    /**
     * Registro de nuevo paciente
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'rut' => 'required|string|max:12|unique:pacientes',
            'email' => 'required|string|email|max:255|unique:pacientes',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'required|string|max:15',
            'direccion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $paciente = Paciente::create([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'rut' => $validated['rut'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telefono' => $validated['telefono'],
            'direccion' => $validated['direccion'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        // Autenticar automáticamente después del registro
        Auth::guard('paciente')->login($paciente);

        return response()->json([
            'user' => $paciente,
            'message' => 'Paciente registrado exitosamente'
        ], 201);
    }

    /**
     * Logout de paciente
     */
    public function logout(Request $request)
    {
        Auth::guard('paciente')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    /**
     * Obtener paciente autenticado
     */
    public function user(Request $request)
    {
        $paciente = Auth::guard('paciente')->user();
        
        if (!$paciente) {
            return response()->json([
                'message' => 'No autenticado'
            ], 401);
        }

        return response()->json([
            'user' => $paciente
        ]);
    }

    /**
     * Actualizar perfil del paciente
     */
    public function updateProfile(Request $request)
    {
        $paciente = Auth::guard('paciente')->user();

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:15',
            'direccion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'sometimes|date',
            'observaciones' => 'nullable|string',
        ]);

        $paciente->update($validated);

        return response()->json([
            'user' => $paciente,
            'message' => 'Perfil actualizado exitosamente'
        ], 200);
    }

    /**
     * Cambiar contraseña del paciente
     */
    public function changePassword(Request $request)
    {
        $paciente = Auth::guard('paciente')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $paciente->password)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta'
            ], 422);
        }

        $paciente->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente'
        ], 200);
    }
}