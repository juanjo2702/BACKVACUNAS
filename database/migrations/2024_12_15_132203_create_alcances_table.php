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
        Schema::create('alcances', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('persona_id'); // Relación con la tabla de personas
            $table->unsignedBigInteger('campania_id'); // Relación con la tabla de campañas
            $table->unsignedBigInteger('zona_id'); // Relación con la tabla de zonas

            // Relaciones
            $table->foreign('persona_id')->references('id')->on('personas');
            $table->foreign('campania_id')->references('id')->on('campanias');
            $table->foreign('zona_id')->references('id')->on('zonas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alcances');
    }
};
