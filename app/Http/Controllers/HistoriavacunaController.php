<?php

namespace App\Http\Controllers;

use App\Models\HistoriaVacuna;
use Illuminate\Http\Request;

class HistoriavacunaController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'estado' => 'required|integer',
            'motivo' => 'nullable|integer',
            'alcance_id' => 'required|exists:alcances,id',
            'participacion_id' => 'required|exists:participacions,id'
        ]);

        try {
            // Crear el historial de vacunas
            $historial = Historiavacuna::create([
                'mascota_id' => $validatedData['mascota_id'],
                'estado' => $validatedData['estado'], // 1 para vacunado, 0 para no vacunado
                'motivo' => $validatedData['estado'] == 0 ? $validatedData['motivo'] : null,
                'alcance_id' => $validatedData['alcance_id'],
                'participacion_id' => $validatedData['participacion_id']
            ]);

            return response()->json(['message' => 'Historial de vacunación guardado correctamente.', 'data' => $historial], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar el historial de vacunación: ' . $e->getMessage()], 500);
        }
    }
}
