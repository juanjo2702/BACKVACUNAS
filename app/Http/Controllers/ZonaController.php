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
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'centro' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'required|string|max:255',
            'estado' => 'nullable|boolean',
        ]);

        try {
            $zona = Zona::findOrFail($id); // Buscar la zona por ID
            $zona->update($request->all()); // Actualizar los datos
            return response()->json(['message' => 'Zona actualizada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar la zona.', 'error' => $e->getMessage()], 500);
        }
    }
    public function getCentros()
    {
        try {
            $centros = Zona::select('id', 'centro')
                ->distinct()
                ->get();

            if ($centros->isEmpty()) {
                return response()->json(['message' => 'No hay centros disponibles'], 404);
            }

            return response()->json($centros, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getZonaDetalles($id)
    {
        $zona = Zona::with(['municipio.red.departamento'])
            ->where('id', $id)
            ->first();

        if (!$zona) {
            return response()->json(['message' => 'Zona no encontrada'], 404);
        }

        return response()->json([
            'centro' => $zona->centro,
            'municipio' => $zona->municipio->nombre,
            'red' => $zona->municipio->red->nombre,
            'departamento' => $zona->municipio->red->departamento->nombre,
        ]);
    }

    public function getByMunicipio(Request $request)
    {
        $municipioId = $request->query('municipio_id');

        if (!$municipioId) {
            return response()->json(['message' => 'El municipio_id es obligatorio'], 400);
        }

        $zonas = Zona::where('municipio_id', $municipioId)->get(['id', 'centro']);

        return response()->json($zonas, 200);
    }
    /* public function desactivar($id)
    {
        try {
            $zona = Zona::findOrFail($id); // Buscar la zona por ID
            $zona->estado = 0; // Cambiar el estado a 0 (inactivo)
            $zona->save(); // Guardar los cambios

            return response()->json(['message' => 'Zona desactivada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al desactivar la zona.', 'error' => $e->getMessage()], 500);
        }
    } */
    public function show($id)
    {
        try {
            $zona = Zona::findOrFail($id); // Busca la zona por ID
            return response()->json($zona, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Zona no encontrada'], 404);
        }
    }
}
