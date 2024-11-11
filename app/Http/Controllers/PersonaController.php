<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    // Método para listar personas
    public function index()
    {
        try {
            $personas = Persona::with('usuario')->get();
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
                'telefono' => 'nullable|string|max:20',
                'usuario_id' => 'nullable|exists:usuarios,id' // Validamos si se envía usuario_id y que exista en la tabla usuarios
            ]);

            // Creación de la nueva persona
            $persona = new Persona();
            $persona->nombres = $validated['nombres'];
            $persona->apellidos = $validated['apellidos'];
            $persona->ci = $validated['ci'] ?? null; // Si 'ci' no es enviado, lo asignamos como null
            $persona->telefono = $validated['telefono'] ?? null; // Lo mismo con 'telefono'
            $persona->usuario_id = $validated['usuario_id'] ?? null; // Asignar el usuario_id si se envía

            $persona->save();

            return response()->json(['message' => 'Persona registrada correctamente', 'persona' => $persona, 'id' => $persona->id], 201); // Código 201 Creado
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
    public function getByUsuarioId($usuario_id)
    {
        // Verifica si el método se está llamando correctamente
        Log::info('Buscando persona para usuario_id: ' . $usuario_id);

        $persona = Persona::where('usuario_id', $usuario_id)->first();

        if (!$persona) {
            Log::error('Persona no encontrada para usuario_id: ' . $usuario_id);
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        Log::info('Persona encontrada: ' . json_encode($persona));
        return response()->json($persona);
    }

    public function show($id)
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        return response()->json($persona);
    }

    public function buscar(Request $request)
    {
        $query = $request->get('q');

        $personas = Persona::where('nombres', 'LIKE', '%' . $query . '%')
            ->orWhere('ci', 'LIKE', '%' . $query . '%')
            ->orWhere('telefono', 'LIKE', '%' . $query . '%')
            ->get();

        return response()->json($personas);
    }
    public function buscarPersonas(Request $request)
    {
        $query = $request->input('q');

        // Buscamos personas por nombres, apellidos, CI o teléfono
        $personas = Persona::where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'LIKE', "%{$query}%")
            ->orWhere('nombres', 'LIKE', "%{$query}%")
            ->orWhere('apellidos', 'LIKE', "%{$query}%")
            ->orWhere('ci', 'LIKE', "%{$query}%")
            ->orWhere('telefono', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($personas);
    }
}
