<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alcance;
use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Support\Facades\Log;

class AlcanceController extends Controller
{
    public function store(Request $request)
    {
        // Captura cualquier excepción durante la validación
        try {
            $validated = $request->validate([
                'campania_id' => 'required|exists:campanias,id',
                'zona_id' => 'required|exists:zonas,id',
                'persona_id' => 'required|exists:personas,id' // Cambiado de usuario_id a persona_id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validación fallida: ', $e->errors());
            return response()->json($e->errors(), 422);
        }

        // Si la validación pasa, continúa con el resto del código
        Log::info('Datos validados correctamente.');

        // Guardar la asignación en la tabla de alcances
        $alcance = new Alcance();
        $alcance->campania_id = $request->input('campania_id');
        $alcance->zona_id = $request->input('zona_id');
        $alcance->persona_id = $request->input('persona_id'); // Relacionar con la persona

        try {
            $alcance->save();
            Log::info('Asignación guardada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al guardar asignación', ['context' => $e->getMessage()]);

            return response()->json(['message' => 'Error al guardar asignación'], 500);
        }

        return response()->json(['message' => 'Asignación guardada correctamente'], 201);
    }
}
