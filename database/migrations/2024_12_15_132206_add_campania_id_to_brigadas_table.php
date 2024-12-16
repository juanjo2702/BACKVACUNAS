<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaniaIdToBrigadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brigadas', function (Blueprint $table) {
            // Añadir la columna campania_id
            $table->unsignedBigInteger('campania_id')->nullable()->after('zona_id');

            // Crear la clave foránea
            $table->foreign('campania_id')
                ->references('id')
                ->on('campanias')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brigadas', function (Blueprint $table) {
            // Eliminar la clave foránea y la columna
            $table->dropForeign(['campania_id']);
            $table->dropColumn('campania_id');
        });
    }
}
