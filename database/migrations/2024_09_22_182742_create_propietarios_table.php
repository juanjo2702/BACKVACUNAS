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
        Schema::create('propietarios', function (Blueprint $table) {
            $table->id();
            $table->string('direccion');
            $table->string('observaciones')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('latitud', 15, 12); // 15 dígitos en total, 12 decimales
            $table->decimal('longitud', 15, 12); // 15 dígitos en total, 12 decimales
            $table->unsignedBigInteger('persona_id');
            $table->timestamps();

            // Relación con tabla 'persona'
            $table->foreign('persona_id')->references('id')->on('personas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propietarios');
    }
};
