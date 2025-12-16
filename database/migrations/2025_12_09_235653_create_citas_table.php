<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            
            // Datos de la cita
            $table->date('fecha');
            $table->time('hora');
            
            // ID del doctor - SIN FOREIGN KEY
            $table->unsignedBigInteger('doctor_id')->nullable();
            
            // O simplemente usa el nombre del doctor como string
            $table->string('doctor_nombre')->nullable();
            
            // ID del paciente
            $table->unsignedBigInteger('paciente_id')->nullable();
            
            // Otros campos
            $table->string('estado')->default('pendiente'); // pendiente, confirmada, cancelada
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            
            // ❌ COMENTAR O ELIMINAR ESTA LÍNEA:
            // $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};