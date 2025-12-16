<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            
            $table->date('fecha');
            $table->time('hora');
            
            // 🆕 Relación con doctores
            $table->unsignedBigInteger('doctor_id');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            
            // Mantener estos campos por compatibilidad (opcional)
            $table->string('doctor_nombre')->nullable();
            $table->string('especialidad')->nullable();
            
            $table->boolean('disponible')->default(true);
            $table->integer('duracion')->default(30);
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            
            $table->index('fecha');
            $table->index('disponible');
            $table->index('doctor_id');
            $table->index(['fecha', 'hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};