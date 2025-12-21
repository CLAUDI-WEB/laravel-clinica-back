<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // Opción 1: Con ENUM (más restrictivo, mejor rendimiento)
            $table->enum('rol', ['admin',  'paciente'])
                  ->default('paciente')
                  ->after('password');
            
            // Opción 2: Con VARCHAR (más flexible)
            // $table->string('rol', 20)->default('paciente')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('rol');
        });
    }
};