<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class PersonaController extends Controller
{
    // Método para listar personas
    public function index()
    {
        try {
            $personas = Persona::all();
            return response()->json($personas, 200); // Respuesta con código 200 OK
        } catch (QueryException $e) {
            return response()->json(['error' => 'Error al obtener personas'], 500); // Manejo de error si algo sale mal
        }
    }

    // Método para guardar una nueva persona
    public function store(Request $request)
    {
        try {
            // Validación de los datos enviados
            $validated = $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'ci' => 'nullable|string|max:20|unique:personas,ci', // validamos para que el campo sea único en la tabla
                'telefono' => 'nullable|string|max:20'
            ]);

            // Creación de la nueva persona
            $persona = new Persona();
            $persona->nombres = $validated['nombres'];
            $persona->apellidos = $validated['apellidos'];
            $persona->ci = $validated['ci'] ?? null; // Si 'ci' no es enviado, lo asignamos como null
            $persona->telefono = $validated['telefono'] ?? null; // Lo mismo con 'telefono'
            $persona->save();

            return response()->json(['message' => 'Persona registrada correctamente', 'persona' => $persona], 201); // Código 201 Creado
        } catch (ValidationException $e) {
            // Capturamos errores de validación
            return response()->json([
                'error' => 'Error de validación',
                'messages' => $e->errors(),
            ], 422); // Código 422 Unprocessable Entity
        } catch (QueryException $e) {
            // Capturamos cualquier error de la base de datos
            return response()->json([
                'error' => 'Error en la base de datos',
                'message' => $e->getMessage(),
            ], 500); // Código 500 Error interno del servidor
        } catch (\Exception $e) {
            // Capturamos cualquier otro error no esperado
            return response()->json([
                'error' => 'Error al registrar la persona',
                'message' => $e->getMessage(),
            ], 500); // Código 500 Error interno del servidor
        }
    }
}
