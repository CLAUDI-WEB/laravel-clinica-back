<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        
        // ⬇️ AGREGAR ESTE GUARD PARA PACIENTES
        'paciente' => [
            'driver' => 'session',
            'provider' => 'pacientes',
        ],
    ],
        'api' => [
        'driver' => 'token',
        'provider' => 'users',
        'hash' => false,
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        
        // ⬇️ AGREGAR ESTE PROVIDER PARA PACIENTES
        'pacientes' => [
            'driver' => 'eloquent',
            'model' => App\Models\Paciente::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        
        // ⬇️ AGREGAR CONFIGURACIÓN DE RESET PASSWORD PARA PACIENTES (OPCIONAL)
        'pacientes' => [
            'provider' => 'pacientes',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];