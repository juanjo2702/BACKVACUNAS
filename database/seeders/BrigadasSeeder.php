<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BrigadasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('brigadas')->insert([
                'usuario_id' => $faker->numberBetween(1, 50), // Suponiendo que hay 50 usuarios
                'zona_id' => $faker->numberBetween(1, 10), // Suponiendo que hay 10 zonas
                'estado' => $faker->numberBetween(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
