<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mascota;
use App\Models\Raza;
use App\Models\Brigada;
use App\Models\Propietario;
use App\Models\Campania;
use App\Models\HistoriaVacuna;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        try {
            // 1. Total de Mascotas
            $totalMascotas = Mascota::count();

            // 2. Mascotas Vacunadas (que tienen una entrada en `historiavacunas`)
            $totalVacunadas = HistoriaVacuna::distinct('mascota_id')->count('mascota_id');

            // 3. Mascotas No Vacunadas (total de mascotas menos las vacunadas)
            $totalNoVacunadas = $totalMascotas - $totalVacunadas;

            // 4. Campañas Activas (usando el campo `estado` en la tabla `campanias`)
            $totalCampanas = Campania::where('estado', 1)->count();

            // 5. Propietarios Registrados
            $totalPropietarios = Propietario::count();

            // 6. Distribución de Mascotas por Especie (usamos `especie` en la tabla `mascotas`)
            $mascotasPorEspecie = Mascota::select('especie', DB::raw('count(*) as count'))
                ->groupBy('especie')
                ->pluck('count', 'especie');

            // 7. Perros Registrados por Raza (usamos `especie = 'perro'` y nos unimos a `razas`)
            $perrosPorRaza = Mascota::where('especie', 'perro')
                ->join('razas', 'mascotas.raza_id', '=', 'razas.id')
                ->select('razas.nombre as raza', DB::raw('count(*) as count'))
                ->groupBy('razas.nombre')
                ->pluck('count', 'raza');

            // 8. Gatos Registrados por Raza (usamos `especie = 'gato'` y nos unimos a `razas`)
            $gatosPorRaza = Mascota::where('especie', 'gato')
                ->join('razas', 'mascotas.raza_id', '=', 'razas.id')
                ->select('razas.nombre as raza', DB::raw('count(*) as count'))
                ->groupBy('razas.nombre')
                ->pluck('count', 'raza');

            // 9. Brigadas Asignadas por Zona (relacionamos `brigadas` con `zonas`)
            $brigadasPorZona = Brigada::join('zonas', 'brigadas.zona_id', '=', 'zonas.id')
            ->select('zonas.nombre as zona', DB::raw('count(brigadas.id) as count'))
            ->groupBy('zonas.nombre')
            ->pluck('count', 'zona');


            // 10. Relación entre Mascotas y Propietarios (gráfico de barras)
            $mascotasRegistradas = $totalMascotas;
            $propietariosRegistrados = $totalPropietarios;

            // NUEVAS CONSULTAS

            // 11. Control de Mascotas Vacunadas y No Vacunadas (gráfico circular)
            $mascotasVacunadasYNo = [
                'vacunadas' => $totalVacunadas,
                'no_vacunadas' => $totalNoVacunadas,
            ];

            // 12. Perros Vacunados y No Vacunados
            $totalPerros = Mascota::where('especie', 'perro')->count();
            $perrosVacunados = HistoriaVacuna::whereHas('mascota', function ($query) {
                $query->where('especie', 'perro');
            })->distinct('mascota_id')->count('mascota_id');
            $perrosNoVacunados = $totalPerros - $perrosVacunados;

            $perrosVacunadosYNo = [
                'vacunados' => $perrosVacunados,
                'no_vacunados' => $perrosNoVacunados,
            ];

            // 13. Gatos Vacunados y No Vacunados
            $totalGatos = Mascota::where('especie', 'gato')->count();
            $gatosVacunados = HistoriaVacuna::whereHas('mascota', function ($query) {
                $query->where('especie', 'gato');
            })->distinct('mascota_id')->count('mascota_id');
            $gatosNoVacunados = $totalGatos - $gatosVacunados;

            $gatosVacunadosYNo = [
                'vacunados' => $gatosVacunados,
                'no_vacunados' => $gatosNoVacunados,
            ];

            // 14. Mascotas Registradas por Zona
/*             $mascotasPorZona = Mascota::join('propietarios', 'mascotas.propietario_id', '=', 'propietarios.id')
            ->join('zonas', 'propietarios.zona_id', '=', 'zonas.id')  // Relacionar propietarios con zonas
            ->select('zonas.nombre as zona', DB::raw('count(mascotas.id) as count'))
            ->groupBy('zonas.nombre')
            ->pluck('count', 'zona'); */


            // Devolver los datos en formato JSON para el frontend
            return response()->json([
                'totalMascotas' => $totalMascotas,
                'totalVacunadas' => $totalVacunadas,
                'totalNoVacunadas' => $totalNoVacunadas,
                'totalCampanas' => $totalCampanas,
                'totalPropietarios' => $totalPropietarios,
                'mascotasPorEspecie' => $mascotasPorEspecie ?? [],
                'perrosPorRaza' => $perrosPorRaza ?? [],
                'gatosPorRaza' => $gatosPorRaza ?? [],
                'brigadasPorZona' => $brigadasPorZona ?? [],
                'mascotasRegistradas' => $mascotasRegistradas ?? 0,
                'propietariosRegistrados' => $propietariosRegistrados ?? 0,
                'mascotasVacunadasYNo' => $mascotasVacunadasYNo ?? [],
                'perrosVacunadosYNo' => $perrosVacunadosYNo ?? [],
                'gatosVacunadosYNo' => $gatosVacunadosYNo ?? [],
/*                 'mascotasPorZona' => $mascotasPorZona ?? [], */
            ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
