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
        Schema::create('zonas', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->string('nombre'); // Nombre de la zona
            $table->string('centro'); // Centro de la zona
            $table->string('ciudad')->nullable(); // Ciudad
            $table->string('departamento')->nullable(); // Departamento
            $table->integer('estado')->default(1); // 1 por defecto para activo
            $table->timestamps(); // Agrega columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
