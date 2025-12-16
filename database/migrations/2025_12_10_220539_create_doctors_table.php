<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('doctors', function (Blueprint $table) {
        $table->id();
        $table->string('rut')->unique();
        $table->string('nombre');
        $table->string('apellido');
        $table->string('email')->unique();
        $table->string('telefono');
        $table->string('especialidad');
        $table->string('numero_registro')->unique(); // Número de registro profesional
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}
