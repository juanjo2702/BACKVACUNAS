<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Http\Requests\StoreMascotaRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateMascotaRequest;

class MascotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las mascotas junto con sus relaciones (propietario y raza)
            $mascotas = Mascota::with(['propietarios', 'razas'])->get();
            return response()->json($mascotas, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la lista de mascotas',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos de la mascota
        $request->validate([
            'nombre' => 'required|string|max:255',
            'genero' => 'required|string|max:10',
            'especie' => 'required|string|max:50',
            'rangoEdad' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'tamanio' => 'required|string|max:50',
            'fotoFrontal' => 'nullable|string',
            'fotoHorizontal' => 'nullable|string',
            'estadoMascota' => 'nullable|string|max:50',
            'estado' => 'nullable|boolean',
            'raza_id' => 'nullable|exists:razas,id',
            'propietario_id' => 'required|exists:propietarios,id'
        ]);

        try {
            // Crear una nueva mascota
            $mascota = Mascota::create($request->all());

            return response()->json([
                'message' => 'Mascota registrada con éxito',
                'mascota' => $mascota
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al registrar la mascota',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mascota $mascota)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mascota $mascota)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMascotaRequest $request, Mascota $mascota)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mascota $mascota)
    {
        //
    }
}
