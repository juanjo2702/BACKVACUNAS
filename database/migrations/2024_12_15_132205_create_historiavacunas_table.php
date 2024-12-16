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
        Schema::create('historiavacunas', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->integer('estado');
            $table->integer('motivo')->nullable();
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('mascota_id'); // Relación con la tabla de mascotas
            $table->unsignedBigInteger('alcance_id'); // Relación con la tabla de alcance
            $table->unsignedBigInteger('participacion_id'); // Relación con la tabla de participación o responsables

            // Relaciones
            $table->foreign('mascota_id')->references('id')->on('mascotas');
            $table->foreign('alcance_id')->references('id')->on('alcances');
            $table->foreign('participacion_id')->references('id')->on('participacions'); // asumiendo que es el miembro o responsable de la participación

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiavacunas');
    }
};
