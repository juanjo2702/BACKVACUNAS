<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MascotasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Obtener todas las razas de perros y gatos
        $razas_perros = DB::table('razas')->where('tipo', 0)->pluck('id')->toArray(); // 0 para perros
        $razas_gatos = DB::table('razas')->where('tipo', 1)->pluck('id')->toArray(); // 1 para gatos

        for ($i = 0; $i < 100; $i++) {
            // Definir el tipo de mascota aleatoriamente
            $tipo_mascota = $faker->randomElement(['perro', 'gato']);

            if ($tipo_mascota === 'perro') {
                $raza_id = $faker->randomElement($razas_perros); // Selecciona una raza de perro
                // Generar una fecha de nacimiento para un perro (más joven de 15 años)
                $rangoEdad = $faker->dateTimeBetween('-15 years', '-1 year')->format('Y-m-d');
            } else {
                $raza_id = $faker->randomElement($razas_gatos); // Selecciona una raza de gato
                // Generar una fecha de nacimiento para un gato (más joven de 15 años)
                $rangoEdad = $faker->dateTimeBetween('-15 years', '-1 year')->format('Y-m-d');
            }

            DB::table('mascotas')->insert([
                'nombre' => $faker->name,
                'genero' => $faker->randomElement(['macho', 'hembra']),
                'especie' => $tipo_mascota,
                'rangoEdad' => $rangoEdad, // Asigna la fecha de nacimiento
                'color' => $faker->colorName,
                'descripcion' => $faker->sentence,
                'tamanio' => $faker->randomElement(['grande', 'mediano', 'pequeño']),
                'raza_id' => $raza_id, // Asigna la raza seleccionada
                'estado' => $faker->numberBetween(0, 1),
                'propietario_id' => $faker->randomElement(DB::table('propietarios')->pluck('id')->toArray()), // Asigna un propietario aleatorio
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
