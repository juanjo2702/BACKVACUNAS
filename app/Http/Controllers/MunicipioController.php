<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index()
    {
        $municipios = Municipio::with('red')->get();
        return response()->json($municipios);
    }

    /**
     * Almacena un nuevo municipio en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'red_id' => 'required|exists:reds,id',
        ]);

        $municipio = Municipio::create($validatedData);
        return response()->json($municipio, 201);
    }

    /**
     * Muestra un municipio específico.
     */
    public function show(Municipio $municipio)
    {
        return response()->json($municipio->load('red'));
    }

    /**
     * Actualiza un municipio existente.
     */
    public function update(Request $request, Municipio $municipio)
    {
        $validatedData = $request->validate([
            'nombre' => 'string|max:255',
            'red_id' => 'exists:reds,id',
        ]);

        $municipio->update($validatedData);
        return response()->json($municipio);
    }

    /**
     * Elimina un municipio de la base de datos.
     */
    public function destroy(Municipio $municipio)
    {
        $municipio->delete();
        return response()->json(['message' => 'Municipio eliminado correctamente.']);
    }

    public function getByRed(Request $request)
    {
        $redId = $request->query('red_id');

        if (!$redId) {
            return response()->json(['message' => 'El red_id es obligatorio'], 400);
        }

        $municipios = Municipio::where('red_id', $redId)->get();

        return response()->json($municipios, 200);
    }
}
