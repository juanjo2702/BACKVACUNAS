<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function index()
    {
        // Devuelve todas las zonas de la base de datos
        return Zona::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del request
        $request->validate([
            'nombre' => 'required|string|max:255',
            'centro' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'required|string|max:255',
        ]);

        // Crear una nueva zona
        $zona = Zona::create([
            'nombre' => strtoupper($request->nombre),  // Convertir el nombre a mayúsculas
            'centro' => strtoupper($request->centro),
            'ciudad' => strtoupper($request->ciudad),
            'departamento' => strtoupper($request->departamento),
        ]);

        // Devolver la zona creada en formato JSON
        return response()->json($zona, 201);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zona $zona)
    {
        // Validar los datos del request
        $request->validate([
            'nombre' => 'required|string|max:255',
            'centro' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'required|string|max:255',
        ]);

        // Actualizar la zona existente
        $zona->update([
            'nombre' => strtoupper($request->nombre),
            'centro' => strtoupper($request->centro),
            'ciudad' => strtoupper($request->ciudad),
            'departamento' => strtoupper($request->departamento),
        ]);

        // Devolver la zona actualizada
        return response()->json($zona, 200);
    }
}
