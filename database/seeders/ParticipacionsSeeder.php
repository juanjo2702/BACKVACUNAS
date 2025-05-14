<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ParticipacionsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Obtener todos los IDs de miembros existentes
        $miembro_ids = DB::table('miembros')->pluck('id')->toArray();

        for ($i = 0; $i < 100; $i++) {
            DB::table('participacions')->insert([
                'brigada_id' => $faker->numberBetween(1, 20), // Suponiendo que hay 20 brigadas
                'miembro_id' => $faker->randomElement($miembro_ids), // Selecciona un miembro existente
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

