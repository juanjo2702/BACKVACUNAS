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
Route::post('/login', [UsuarioController::class, 'login']);
Route::apiResource('/personas',PersonaController::class);
Route::apiResource('/propietarios', PropietarioController::class);
Route::apiResource('/razas', RazaController::class);
Route::apiResource('/mascotas', MascotaController::class);
Route::apiResource('/rols', RolController::class);
Route::apiResource('usuarios', UsuarioController::class);
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

Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);
