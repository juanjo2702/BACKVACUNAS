<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ZonasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('zonas')->insert([
                'nombre' => $faker->city,
                'centro' => $faker->address,
                'ciudad' => $faker->city,
                'departamento' => $faker->state,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
