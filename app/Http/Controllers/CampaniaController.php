<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campania;
use App\Models\Brigada;
use Illuminate\Support\Facades\Log;

class CampaniaController extends Controller
{
    public function index()
    {
        $campanias = Campania::all();
        return response()->json($campanias);
    }

    // Función para guardar una nueva campaña
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date|after_or_equal:fechaInicio',
            'estado' => 'required|integer',
        ]);

        $campania = Campania::create($validatedData);

        return response()->json([
            'message' => 'Campaña creada exitosamente',
            'campania' => $campania
        ], 201);
    }

    // Función para actualizar una campaña
    public function update(Request $request, $id)
    {
        $campania = Campania::find($id);

        if (!$campania) {
            return response()->json([
                'message' => 'Campaña no encontrada'
            ], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'fechaInicio' => 'sometimes|required|date',
            'fechaFinal' => 'sometimes|required|date|after_or_equal:fechaInicio',
            'estado' => 'sometimes|required|integer',
        ]);

        $campania->update($validatedData);

        return response()->json([
            'message' => 'Campaña actualizada exitosamente',
            'campania' => $campania
        ]);
    }

    public function getBrigadasConMiembros($campaniaId)
    {
        try {
            $brigadas = Brigada::where('campania_id', $campaniaId)
                ->with([
                    'zona:id,id,centro',
                    'usuario:id,nombre',
                    'miembros.persona:id,nombres,apellidos,ci,telefono'
                ])
                ->get()
                ->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'nombre' => $b->usuario->nombre ?? 'Sin nombre',
                        'centro' => optional($b->zona)->centro ?? 'Centro no definido',
                        'miembros' => $b->miembros->map(function ($m) {
                            return [
                                'id' => $m->id,
                                'persona' => $m->persona
                            ];
                        })
                    ];
                });

            return response()->json($brigadas);
        } catch (\Exception $e) {
            Log::error('Error al obtener brigadas con miembros: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo obtener brigadas'], 500);
        }
    }
}
