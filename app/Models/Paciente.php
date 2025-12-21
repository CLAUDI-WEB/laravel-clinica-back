<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Paciente extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'pacientes';

    protected $fillable = [
        'rut',
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'fecha_nacimiento',
        'direccion',
        'observaciones',
        'rol', // Agregar rol
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
        ];
    }

    // Constantes para los roles
    const ROL_ADMIN = 'admin';
    const ROL_PACIENTE = 'paciente';

    // Métodos helper para verificar roles
    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function isPaciente(): bool
    {
        return $this->rol === self::ROL_PACIENTE;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->rol, $roles);
        }
        return $this->rol === $roles;
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }
}