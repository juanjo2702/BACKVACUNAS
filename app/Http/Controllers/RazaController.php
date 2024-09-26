<?php

namespace App\Http\Controllers;

use App\Models\Raza;
use App\Http\Requests\StoreRazaRequest;
use App\Http\Requests\UpdateRazaRequest;

class RazaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las razas
            $razas = Raza::all();

            // Retornar la respuesta en formato JSON
            return response()->json($razas, 200);
        } catch (\Exception $e) {
            // Manejar cualquier error inesperado
            return response()->json([
                'error' => 'Error al obtener la lista de razas',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRazaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Raza $raza)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Raza $raza)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRazaRequest $request, Raza $raza)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Raza $raza)
    {
        //
    }
}
