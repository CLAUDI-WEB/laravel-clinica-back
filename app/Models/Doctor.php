<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'especialidad',
        'numero_registro',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}