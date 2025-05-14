<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
/*             UsuariosSeeder::class,
            PersonasSeeder::class, */
       /*      MiembrosSeeder::class,
            PropietariosSeeder::class,
            ZonasSeeder::class, */
/*             MascotasSeeder::class,
            BrigadasSeeder::class,
            CampaniasSeeder::class, */
          /*   AlcancesSeeder::class, */
         /*    ParticipacionsSeeder::class, */
            HistoriasVacunasSeeder::class,
        ]);
    }
}
