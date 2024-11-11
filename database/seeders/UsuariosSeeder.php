<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            DB::table('usuarios')->insert([
                'nombre' => $faker->name, // Añadir el campo nombre
                'password' => bcrypt($faker->password), // Hashear la contraseña
                'rol_id' => $faker->numberBetween(1, 3), // Suponiendo que hay 3 roles
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
