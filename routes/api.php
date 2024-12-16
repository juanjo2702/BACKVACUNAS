<?php

use App\Http\Controllers\AlcanceController;
use App\Http\Controllers\BrigadaController;
use App\Http\Controllers\CampaniaController;
use App\Http\Controllers\HistoriavacunaController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\ParticipacionController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PropietarioController;
use App\Http\Controllers\RazaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\RedController;
use App\Http\Controllers\MunicipioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/zonas/centros', [ZonaController::class, 'getCentros']);
Route::post('/login', [UsuarioController::class, 'login']);
Route::apiResource('/personas', PersonaController::class);
Route::apiResource('/propietarios', PropietarioController::class);
Route::apiResource('/razas', RazaController::class);
Route::apiResource('/mascotas', MascotaController::class);
Route::apiResource('/rols', RolController::class);
Route::apiResource('/usuarios', UsuarioController::class);
Route::apiResource('/zonas', ZonaController::class);
Route::apiResource('/campanias', CampaniaController::class);
Route::apiResource('/brigadas', BrigadaController::class);
Route::apiResource('/miembros', MiembroController::class);
Route::apiResource('/alcances', AlcanceController::class);
Route::apiResource('/participacions', ParticipacionController::class);
Route::apiResource('/historiavacunas', HistoriavacunaController::class);

Route::get('/jefes-zona', [UsuarioController::class, 'getJefesZona']);
Route::get('/personas/{usuario_id}', [PersonaController::class, 'getByUsuarioId']);
Route::get('/personas/usuario/{usuario_id}', [PersonaController::class, 'getByUsuarioId']);
Route::get('/brigadas/usuario/{usuarioId}', [BrigadaController::class, 'getBrigadaByUsuario']);
Route::get('/mascotas/propietario/{propietarioId}', [MascotaController::class, 'getByPropietario']);
/* Route::get('/propietarios/{propietarioId}/mascotas', [PropietarioController::class, 'getPropietarioConMascotas']); */
Route::get('/razas', [RazaController::class, 'getRazas']);
Route::get('brigadas/{id}/miembros', [BrigadaController::class, 'getMiembrosBrigada']);
Route::get('/mascota/{id}/raza', [MascotaController::class, 'obtenerRazaPorMascota']);
// Ruta para buscar personas o propietarios
Route::get('/buscar-personas', [PropietarioController::class, 'buscarPersonas']);
//Route::post('/historiavacunas', [HistoriavacunaController::class, 'guardarHistorialVacuna']);
// Ruta para obtener las mascotas de un propietario
Route::get('/propietario/{id}/mascotas', [PropietarioController::class, 'obtenerMascotas']);
Route::get('/buscar-personas', [PersonaController::class, 'buscarPersonas']);
Route::get('/mascotas/{id}', [MascotaController::class, 'show']);

Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);
Route::get('/mascota/{mascotaId}/historial-vacunas', [MascotaController::class, 'obtenerHistorialVacunasPorMascota']);

Route::get('/propietarios/{id}', [PropietarioController::class, 'show']);
Route::get('/personas/{id}', [PersonaController::class, 'show']);
Route::get('/propietarios/{propietario_id}/mascotas', [MascotaController::class, 'getMascotasByPropietario']);
Route::get('/campanias/{id}/zonas', [CampaniaController::class, 'getZonasByCampania']);
Route::get('/campanias/{id}/brigadas', [CampaniaController::class, 'getBrigadasByCampania']);
Route::get('/alcances/{campaniaId}/zonas', [AlcanceController::class, 'getZonasByCampania']);
Route::get('/alcances/campania/{campaniaId}', [AlcanceController::class, 'getZonasByCampania']);
Route::get('/mascota/{mascotaId}/historial-vacunas', [HistoriavacunaController::class, 'getHistorialPorMascota']);
Route::post('/brigadas/generar', [BrigadaController::class, 'generarBrigadas']);
Route::get('/usuarios-filtro', [UsuarioController::class, 'filtrarPorRolEstado']);
Route::get('/personas/{id}', [PersonaController::class, 'show']);
Route::put('/jefe-zona/{id}/update', [PersonaController::class, 'updateJefeZona']);

//RECIEN AÑADIDO JUANPA

Route::patch('/zonas/{id}/desactivar', [ZonaController::class, 'desactivar']);

//RECIEN AÑADIDO
Route::get('/jefe-zona/{id}', [PersonaController::class, 'show']);
Route::put('/jefe-zona/{id}', [PersonaController::class, 'update']);
Route::put('/personas/{id}', [PersonaController::class, 'update']);
Route::patch('/personas/{id}/desactivar', [PersonaController::class, 'desactivar']);
Route::get('/propietarios/{id}/with-person', [PropietarioController::class, 'showWithPerson']);
Route::get('/persona/{personaId}/propietario', [PersonaController::class, 'obtenerPropietarioPorPersona']);
Route::get('/personas-con-propietarios', [PersonaController::class, 'obtenerPersonasConPropietario']);
Route::get('/storage/{path}', function ($path) {
    $path = storage_path('app/public/' . $path);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->where('path', '.*');

Route::put('/propietarios/{id}', [PropietarioController::class, 'update']);

Route::get('/miembros', [MiembroController::class, 'getByPersonaId']);
Route::get('/participacions', [ParticipacionController::class, 'checkParticipacion']);
Route::get('/participaciones/brigada/{brigadaId}', [ParticipacionController::class, 'getParticipacionesByBrigada']);
Route::get('/personas?filter=withUsuario', action: [PersonaController::class, 'indexWithUsuario']);
Route::patch('/mascotas/{id}/desactivar', [MascotaController::class, 'desactivar']);
Route::get('/getRazas', [RazaController::class, 'getRazas']);
Route::put('/mascotas/{id}', [MascotaController::class, 'update']);
Route::post('/participaciones/registrar', [ParticipacionController::class, 'registrarParticipacion']);
Route::post('/personas/buscar-por-ci', [PersonaController::class, 'buscarPorCI']);

Route::get('/zonas/{id}/detalles', [ZonaController::class, 'getZonaDetalles']);

// Rutas para cargar datos dinámicos
Route::get('/departamentos', [DepartamentoController::class, 'index']);
Route::get('/redes', [RedController::class, 'getByDepartamento']);
Route::get('/municipios', [MunicipioController::class, 'getByRed']);
Route::get('/zonas',[ZonaController::class, 'getByMunicipio']);
