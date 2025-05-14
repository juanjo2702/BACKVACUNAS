<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PropietariosSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            DB::table('propietarios')->insert([
                'direccion' => $faker->address,
                'observaciones' => $faker->sentence,
                'foto' => $faker->imageUrl(),
                'latitud' => $faker->latitude,
                'longitud' => $faker->longitude,
                'persona_id' => $faker->numberBetween(1, 100), // Suponiendo que hay 100 personas
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
