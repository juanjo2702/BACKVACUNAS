<?php

namespace App\Http\Controllers;

use App\Models\Participacion;
use App\Models\Persona;
use App\Models\Miembro;
use Illuminate\Http\Request;

class ParticipacionController extends Controller
{
    public function store(Request $request)
    {
        // Validar que el miembro_id y brigada_id son válidos y existen
        $request->validate([
            'miembro_id' => 'required|exists:miembros,id',
            'brigada_id' => 'required|exists:brigadas,id',
        ]);

        // Crear la participación
        $participacion = Participacion::create([
            'miembro_id' => $request->miembro_id,
            'brigada_id' => $request->brigada_id,
        ]);

        return response()->json(['message' => 'Participación registrada con éxito', 'participacion' => $participacion]);
    }

    public function checkParticipacion(Request $request)
    {
        $miembro_id = $request->input('miembro_id');
        $brigada_id = $request->input('brigada_id');

        $participacion = Participacion::where('miembro_id', $miembro_id)
            ->where('brigada_id', $brigada_id)
            ->first();

        if ($participacion) {
            return response()->json(['message' => 'Ya está registrado en esta brigada'], 200); // Participación encontrada
        }

        return response()->json(['message' => 'No registrado en esta brigada'], 404); // Participación no encontrada
    }
    // ParticipacionController.php
    public function getParticipacionesByBrigada($brigadaId)
    {
        $participaciones = Participacion::where('brigada_id', $brigadaId)
            ->with('miembro.persona') // Cargar relaciones
            ->get();

        return response()->json($participaciones);
    }
    public function registrarParticipacion(Request $request)
    {
        $request->validate([
            'ci' => 'required|string',
        ]);

        // Buscar la persona por CI
        $persona = Persona::where('ci', $request->ci)->first();

        if (!$persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        // Verificar si la persona es un miembro
        $miembro = Miembro::where('persona_id', $persona->id)->first();

        if (!$miembro) {
            return response()->json(['error' => 'La persona no es un miembro'], 404);
        }

        // Registrar la participación
        $participacion = Participacion::create([
            'miembro_id' => $miembro->id,
            'brigada_id' => $request->brigada_id,
        ]);

        return response()->json(['message' => 'Participación registrada con éxito', 'participacion' => $participacion]);
    }
}
