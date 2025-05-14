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
        Schema::create('brigadas', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('usuario_id'); // Relación con la tabla de usuarios
            $table->unsignedBigInteger('zona_id'); // Relación con la tabla de zonas
            $table->integer('estado')->default(1); // Estado de la brigada (activo/inactivo)

            // Relaciones
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('zona_id')->references('id')->on('zonas');

            $table->timestamps(); // Agrega columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brigadas');
    }
};
