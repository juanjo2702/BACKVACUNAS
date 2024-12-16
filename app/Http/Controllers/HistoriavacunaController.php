<?php

namespace App\Http\Controllers;

use App\Models\Historiavacuna;
use App\Models\Brigada;
use App\Models\Alcance;
use App\Models\Campania;
use App\Models\Participacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Importar el logging de Laravel

class HistoriavacunaController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validación de los datos del request
            $request->validate([
                'estado' => 'required|integer',
                'motivo' => 'nullable|integer', // Solo si es no vacunado
                'descripcion' => 'nullable|string|required_if:motivo,3', // Requerido solo si el motivo es "Otro" (3)
                'mascota_id' => 'required|exists:mascotas,id',
                'miembro_id' => 'required|exists:miembros,id',
                'brigada_id' => 'required|exists:brigadas,id' // Validamos que brigada_id esté presente y exista
            ]);

            // Obtener la última participación correspondiente al miembro
            $participacion = Participacion::where('miembro_id', $request->miembro_id)
                ->latest('created_at')
                ->first();

            if (!$participacion) {
                return response()->json(['error' => 'No se encontró la participación del miembro.'], 404);
            }

            // Buscar la brigada con el `brigada_id` recibido desde el frontend
            Log::info('Buscando la brigada con ID: ' . $request->brigada_id);
            $brigada = Brigada::find($request->brigada_id);

            if ($brigada) {
                // Obtener la zona_id y campania_id de la brigada
                $zonaId = $brigada->zona_id;
                $campaniaId = $brigada->campania_id;

                // Buscar el alcance donde coincidan zona_id y campania_id
                $alcance = Alcance::where('zona_id', $zonaId)
                    ->where('campania_id', $campaniaId)
                    ->first();

                if (!$alcance) {
                    return response()->json(['error' => 'No se encontró el alcance para la zona y campaña especificadas.'], 404);
                }
            } else {
                return response()->json(['error' => 'No se encontró la brigada.'], 404);
            }

            // Log antes de crear el registro
            Log::info('Creando el registro en la tabla historiavacunas con los siguientes datos:', [
                'estado' => $request->estado,
                'motivo' => $request->motivo,
                'descripcion' => $request->descripcion,
                'mascota_id' => $request->mascota_id,
                'participacion_id' => $participacion->id,
                'alcance_id' => $alcance->id
            ]);

            // Crear el registro en la tabla `historiavacunas`
            Historiavacuna::create([
                'estado' => $request->estado, // 1 para vacunado, 0 para no vacunado
                'motivo' => $request->estado == 0 ? $request->motivo : null, // Solo si no está vacunado
                'descripcion' => $request->motivo == 3 ? $request->descripcion : null, // Guardar descripción solo si el motivo es 3
                'mascota_id' => $request->mascota_id,
                'participacion_id' => $participacion->id,
                'alcance_id' => $alcance->id // Usar el alcance correcto
            ]);

            Log::info('Registro creado en historiavacunas:', [
                'estado' => $request->estado,
                'motivo' => $request->motivo,
                'descripcion' => $request->descripcion,
                'mascota_id' => $request->mascota_id
            ]);

            return response()->json(['message' => 'Historial de vacunación guardado correctamente.'], 200);
        } catch (\Exception $e) {
            // Log para capturar cualquier error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getHistorialPorMascota($mascotaId)
    {
        try {
            $historial = Historiavacuna::where('mascota_id', $mascotaId)
                ->join('alcances', 'historiavacunas.alcance_id', '=', 'alcances.id')
                ->join('campanias', 'alcances.campania_id', '=', 'campanias.id')
                ->select(
                    'historiavacunas.estado',
                    'historiavacunas.motivo',
                    'historiavacunas.descripcion',
                    'historiavacunas.created_at',
                    'campanias.nombre as campania_nombre'
                )
                ->orderBy('historiavacunas.created_at', 'desc') // Orden descendente
                ->take(3) // Tomar los 3 últimos registros
                ->get();

            return response()->json($historial, 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo el historial de vacunación: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo obtener el historial de vacunación.'], 500);
        }
    }


            public function index()
    {
        // Si necesitas devolver un listado de datos, ajusta este método
        return response()->json(['message' => 'Historial de vacunas listado correctamente']);
    }

}
