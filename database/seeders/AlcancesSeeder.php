<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AlcancesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            DB::table('alcances')->insert([
                'persona_id' => $faker->numberBetween(1, 100), // Suponiendo que hay 100 personas
                'campania_id' => $faker->numberBetween(1, 10), // Suponiendo que hay 10 campañas
                'zona_id' => $faker->numberBetween(1, 10), // Suponiendo que hay 10 zonas
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
