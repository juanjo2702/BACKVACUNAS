<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MiembrosSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Supongamos que tienes un total de 50 personas y quieres que solo 10 sean miembros
        $persona_ids = DB::table('personas')->pluck('id')->toArray(); // Obtener todos los IDs de personas

        // Selecciona aleatoriamente 10 IDs de la lista
        $miembro_ids = array_rand(array_flip($persona_ids), 10); // Cambiar 10 al número de miembros que deseas

        foreach ($miembro_ids as $persona_id) {
            DB::table('miembros')->insert([
                'fotoAnverso' => $faker->imageUrl(),
                'fotoReverso' => $faker->imageUrl(),
                'estado' => $faker->numberBetween(0, 1),
                'persona_id' => $persona_id, // Asigna solo los IDs de miembros seleccionados
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
