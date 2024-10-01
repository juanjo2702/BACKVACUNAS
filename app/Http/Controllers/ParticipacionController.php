<?php

namespace App\Http\Controllers;

use App\Models\Participacion;
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
}

