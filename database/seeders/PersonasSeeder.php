<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PersonasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            DB::table('personas')->insert([
                'nombres' => $faker->firstName(),
                'apellidos' => $faker->lastName(),
                'ci' => $faker->unique()->numberBetween(1000000, 9999999),
                'telefono' => $faker->phoneNumber(),
                'usuario_id' => $faker->numberBetween(1, 50), // Suponiendo que hay 50 usuarios
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
