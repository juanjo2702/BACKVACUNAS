<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mascota;
use App\Models\Campania;
use App\Models\Propietario;
use App\Models\HistoriaVacuna;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        // Total de mascotas
        $totalMascotas = Mascota::count();

        // Mascotas vacunadas
        $totalVacunadas = HistoriaVacuna::distinct('mascota_id')->count('mascota_id');

        // Mascotas no vacunadas
        $totalNoVacunadas = $totalMascotas - $totalVacunadas;

        // Campañas activas
        $totalCampanas = Campania::where('estado', 1)->count();

        // Propietarios registrados
        $totalPropietarios = Propietario::count();

        // Distribución de vacunación por zona
        // En este caso, estamos asumiendo que las mascotas están vinculadas a las brigadas,
        // y las brigadas a las zonas.
        $vacunasPorZona = DB::table('mascotas')
            ->join('brigadas', 'brigadas.id', '=', 'mascotas.brigada_id') // Asegúrate que tienes esta columna en la tabla mascotas
            ->join('zonas', 'zonas.id', '=', 'brigadas.zona_id')
            ->select('zonas.nombre as zona', DB::raw('count(*) as count'))
            ->groupBy('zonas.nombre')
            ->pluck('count', 'zona');

        return response()->json([
            'totalMascotas' => $totalMascotas,
            'totalVacunadas' => $totalVacunadas,
            'totalNoVacunadas' => $totalNoVacunadas,
            'totalCampanas' => $totalCampanas,
            'totalPropietarios' => $totalPropietarios,
            'vacunasPorZona' => $vacunasPorZona,
        ]);
    }
}
