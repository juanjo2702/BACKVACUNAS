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
        Schema::create('participacions', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('brigada_id'); // Relación con la tabla de brigadas
            $table->unsignedBigInteger('miembro_id'); // Relación con la tabla de miembros

            // Relaciones
            $table->foreign('brigada_id')->references('id')->on('brigadas');
            $table->foreign('miembro_id')->references('id')->on('miembros');

            $table->timestamps(); // Agrega columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participacions');
    }
};
