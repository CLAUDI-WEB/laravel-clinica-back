<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
        {
            Schema::create('pacientes', function (Blueprint $table) {
                $table->id();
                $table->string('rut')->unique();
                $table->string('nombre');
                $table->string('apellido');
                $table->string('email')->nullable();
                $table->string('telefono');
                $table->date('fecha_nacimiento');
                $table->text('direccion')->nullable();
                $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('pacientes');
    }
}
