<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MiembroController extends Controller
{
    /**
     * Registrar un nuevo miembro.
     */
    public function store(Request $request)
    {
        // Log para revisar todos los datos enviados al controlador
        Log::info('Datos recibidos en el controlador Miembro:', $request->all());

        // Validación de los datos
        $request->validate([
            'persona_id' => 'required|exists:personas,id', // Verificar que el persona_id exista
            'fotoAnverso' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validar foto anverso
            'fotoReverso' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validar foto reverso
        ]);

        try {
            // Manejar la subida de la imagen del anverso
            if ($request->hasFile('fotoAnverso')) {
                $anversoPath = $request->file('fotoAnverso')->store('images/miembros', 'public');
                Log::info('Anverso del CI subido:', ['path' => $anversoPath]);
            }

            // Manejar la subida de la imagen del reverso
            if ($request->hasFile('fotoReverso')) {
                $reversoPath = $request->file('fotoReverso')->store('images/miembros', 'public');
                Log::info('Reverso del CI subido:', ['path' => $reversoPath]);
            }

            // Crear un nuevo miembro
            $miembro = Miembro::create([
                'persona_id' => $request->input('persona_id'),
                'fotoAnverso' => $anversoPath ?? null,
                'fotoReverso' => $reversoPath ?? null,
                'estado' => 1,  // Activo por defecto
            ]);

            return response()->json([
                'message' => 'Miembro registrado con éxito',
                'id' => $miembro->id,
                'miembro' => $miembro
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al registrar el miembro:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Error al registrar el miembro',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getByPersonaId(Request $request)
    {
        $persona_id = $request->input('persona_id');

        // Buscar miembro por persona_id
        $miembro = Miembro::where('persona_id', $persona_id)->first();

        if ($miembro) {
            return response()->json($miembro, 200); // Miembro encontrado
        }

        return response()->json(null, 404); // Miembro no encontrado
    }
}
