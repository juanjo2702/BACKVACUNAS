<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolsSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Admin', 'Usuario', 'Miembro'];

        foreach ($roles as $role) {
            DB::table('rols')->insert([
                'nombre' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
