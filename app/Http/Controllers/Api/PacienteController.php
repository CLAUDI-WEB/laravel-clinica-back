<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller\Api;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return response()->json($pacientes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut' => 'required|unique:pacientes',
            'nombre' => 'required',
            'apellido' => 'required',
            'telefono' => 'required',
            'fecha_nacimiento' => 'required|date',
            'email' => 'nullable|email',
            'direccion' => 'nullable',
            'observaciones' => 'nullable',
        ]);

        $paciente = Paciente::create($validated);
        return response()->json($paciente, 201);
    }

    public function show($id)
    {
        $paciente = Paciente::findOrFail($id);
        return response()->json($paciente);
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);
        
        $validated = $request->validate([
            'rut' => 'required|unique:pacientes,rut,' . $id,
            'nombre' => 'required',
            'apellido' => 'required',
            'telefono' => 'required',
            'fecha_nacimiento' => 'required|date',
            'email' => 'nullable|email',
            'direccion' => 'nullable',
            'observaciones' => 'nullable',
        ]);
        
        $paciente->update($validated);
        return response()->json($paciente);
    }

    public function destroy($id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->delete();
        return response()->json(['message' => 'Paciente eliminado correctamente']);
    }
}