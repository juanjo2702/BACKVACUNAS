<?php

namespace App\Http\Controllers;

use App\Models\Raza;
use App\Http\Requests\StoreRazaRequest;
use App\Http\Requests\UpdateRazaRequest;
use Illuminate\Http\Request;

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

    public function getRazas(Request $request)
    {
        // Validamos que el parámetro tipo sea válido y que esté presente
        $tipo = $request->query('tipo'); // 'tipo' puede ser 0 para perros, 1 para gatos

        if (!in_array($tipo, ['0', '1'])) {
            return response()->json(['error' => 'Tipo no válido.'], 400); // En caso de que el tipo no sea válido
        }

        // Obtenemos las razas filtrando por tipo
        $razas = Raza::where('tipo', $tipo)->get();

        // Retornamos las razas como respuesta en formato JSON
        return response()->json($razas);
    }
}
