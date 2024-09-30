<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('genero');
            $table->string('especie');
            $table->date('rangoEdad');
            $table->string('color');
            $table->string('descripcion')->nullable();
            $table->string('tamanio');
            $table->string('fotoFrontal')->nullable();
            $table->string('fotoHorizontal')->nullable();
            $table->integer('estado')->default(1);
            $table->unsignedBigInteger('raza_id');
            $table->unsignedBigInteger('propietario_id');
            $table->timestamps();

            // Relación con la tabla 'raza'
            $table->foreign('raza_id')->references('id')->on('razas');
            $table->foreign('propietario_id')->references('id')->on('propietarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
