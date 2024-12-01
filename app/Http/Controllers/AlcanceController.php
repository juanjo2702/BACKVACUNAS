<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alcance;
use App\Models\Usuario;
use App\Models\Zona;
use Illuminate\Support\Facades\Log;

class AlcanceController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos
        try {
            $validated = $request->validate([
                'campania_id' => 'required|exists:campanias,id',
                'zona_id' => 'required|exists:zonas,id',
                'persona_id' => 'required|exists:personas,id' // Cambiado de usuario_id a persona_id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json($e->errors(), 422);
        }

        // Verificar si ya existe la asignación
        $exists = Alcance::where('campania_id', $request->campania_id)
            ->where('zona_id', $request->zona_id)
            ->exists();

        if ($exists) {
            // Enviar mensaje de error si ya existe
            return response()->json([
                'message' => 'El centro ya se encuentra asignado a esta campaña.'
            ], 409); // Código HTTP 409: Conflicto
        }

        // Crear una nueva asignación
        $alcance = new Alcance();
        $alcance->campania_id = $request->input('campania_id');
        $alcance->zona_id = $request->input('zona_id');
        $alcance->persona_id = $request->input('persona_id'); // Relacionar con la persona

        try {
            $alcance->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar asignación.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Asignación guardada correctamente.'
        ], 201);
    }

    public function getZonasByCampania($campaniaId)
    {
        try {
            // Obtener los alcances relacionados con la campaña
            $alcances = Alcance::where('campania_id', $campaniaId)
                ->with('zona:id,centro') // Relación con la tabla zonas
                ->get();

            if ($alcances->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron zonas para esta campaña.',
                ], 404);
            }

            // Devolver las zonas relacionadas
            return response()->json($alcances, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener zonas asociadas a la campaña.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCentros()
    {
        return response()->json(Zona::select('id', 'centro')->distinct()->get());
    }
}
