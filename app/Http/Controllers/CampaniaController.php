<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campania;
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

}
