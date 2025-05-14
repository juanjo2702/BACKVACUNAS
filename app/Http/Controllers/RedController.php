<?php

namespace App\Http\Controllers;

use App\Models\Red;
use Illuminate\Http\Request;

class RedController extends Controller
{
    public function index()
    {
        $reds = Red::with('departamento')->get();
        return response()->json($reds);
    }

    /**
     * Almacena una nueva red en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'departamento_id' => 'required|exists:departamentos,id',
        ]);

        $red = Red::create($validatedData);
        return response()->json($red, 201);
    }

    /**
     * Muestra una red específica.
     */
    public function show(Red $red)
    {
        return response()->json($red->load('departamento'));
    }

    /**
     * Actualiza una red existente.
     */
    public function update(Request $request, Red $red)
    {
        $validatedData = $request->validate([
            'nombre' => 'string|max:255',
            'departamento_id' => 'exists:departamentos,id',
        ]);

        $red->update($validatedData);
        return response()->json($red);
    }

    /**
     * Elimina una red de la base de datos.
     */
    public function destroy(Red $red)
    {
        $red->delete();
        return response()->json(['message' => 'Red eliminada correctamente.']);
    }

    public function getByDepartamento(Request $request)
    {
        $departamentoId = $request->query('departamento_id');

        if (!$departamentoId) {
            return response()->json(['message' => 'El departamento_id es obligatorio'], 400);
        }

        $redes = Red::where('departamento_id', $departamentoId)->get();

        return response()->json($redes, 200);
    }
}
