<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class HistoriasVacunasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Obtener todos los IDs de mascotas y participaciones existentes
        $mascota_ids = DB::table('mascotas')->pluck('id')->toArray(); // Obtener IDs de mascotas
        $participacion_ids = DB::table('participacions')->pluck('id')->toArray(); // Obtener IDs de participaciones

        for ($i = 0; $i < 100; $i++) {
            DB::table('historiavacunas')->insert([
                'estado' => $faker->numberBetween(0, 1),
                'motivo' => $faker->numberBetween(1, 5), // Suponiendo que hay 5 motivos
                'mascota_id' => $faker->randomElement($mascota_ids), // Selecciona un ID de mascota existente
                'participacion_id' => $faker->randomElement($participacion_ids), // Selecciona un ID de participación existente
                'alcance_id' => $faker->numberBetween(1, 50), // Suponiendo que hay 50 alcances
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
