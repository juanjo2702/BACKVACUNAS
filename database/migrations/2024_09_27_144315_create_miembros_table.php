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
        Schema::create('miembros', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->string('fotoAnverso'); // Foto del anverso
            $table->string('fotoReverso'); // Foto del reverso
            $table->integer('estado')->nullable(); // Estado del miembro
            $table->unsignedBigInteger('persona_id'); // Relación con la tabla de personas

            // Relaciones
            $table->foreign('persona_id')->references('id')->on('personas');

            $table->timestamps(); // Agrega columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miembros');
    }
};
