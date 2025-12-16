<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller\Api;
use App\Models\Doctor;
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

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        return response()->json(['message' => 'Doctor eliminado correctamente']);
    }
}