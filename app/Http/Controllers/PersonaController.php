<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function index(Request $request)
{
    if ($request->has('filter') && $request->filter === 'withUsuario') {
        // Filtrar personas con usuarios y roles específicos
        $personas = Persona::with(['usuario' => function ($query) {
            $query->where('rol_id', 2); // Rol JEFEZONA (ajusta según tu ID de rol)
        }])->whereHas('usuario')->get();

        return response()->json($personas);
    }

    // Si no hay filtro, retorna todas las personas
    $personas = Persona::with('propietario')->get();
    return response()->json($personas);
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
        // Buscar la persona junto con su usuario relacionado
        $persona = Persona::with('usuario')->find($id);

        // Validar si la persona existe
        if (!$persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        return response()->json($persona); // Retornar la persona junto con los datos del usuario
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
    public function update(Request $request, $id)
{
    $persona = Persona::find($id);

    if (!$persona) {
        return response()->json(['error' => 'Persona no encontrada'], 404);
    }

    // Validar los datos recibidos
    $validatedData = $request->validate([
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'ci' => 'nullable|string|max:20|unique:personas,ci,' . $id, // Permitir el mismo CI si no cambia
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'observaciones' => 'nullable|string|max:1000',
        'latitud' => 'nullable|numeric',
        'longitud' => 'nullable|numeric',
    ]);

    try {
        // Actualizar los datos de la persona
        $persona->update([
            'nombres' => $validatedData['nombres'],
            'apellidos' => $validatedData['apellidos'],
            'ci' => $validatedData['ci'],
            'telefono' => $validatedData['telefono'],
        ]);

        // Actualizar o crear el propietario relacionado
        $persona->propietario()->updateOrCreate(
            ['persona_id' => $id],
            [
                'direccion' => $validatedData['direccion'],
                'observaciones' => $validatedData['observaciones'],
                'latitud' => $validatedData['latitud'],
                'longitud' => $validatedData['longitud'],
            ]
        );

        return response()->json(['message' => 'Persona y propietario actualizados correctamente'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al actualizar los datos', 'details' => $e->getMessage()], 500);
    }
}




    public function desactivar($id)
    {
        try {
            $persona = Persona::findOrFail($id); // Buscar la persona por ID
            $persona->estado = 0; // Cambiar el estado a inactivo
            $persona->save(); // Guardar los cambios

            return response()->json(['message' => 'Persona desactivada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al desactivar la persona.', 'error' => $e->getMessage()], 500);
        }
    }

    public function obtenerPropietarioPorPersona($personaId)
{
    $persona = Persona::find($personaId);

    if (!$persona) {
        return response()->json(['error' => 'Persona no encontrada'], 404);
    }

    $propietario = $persona->propietario; // Relación entre persona y propietario

    if (!$propietario) {
        return response()->json(['error' => 'Propietario no asociado a esta persona'], 404);
    }

    return response()->json($propietario);
}
public function obtenerPersonasConPropietario(Request $request)
{
    // Obtiene las personas con estado 1 y sus propietarios
    $personas = Persona::where('estado', 1) // Filtrar solo personas activas
        ->with('propietario') // Incluir datos del propietario asociado
        ->get();

    // Formateamos los datos para incluir persona_id dentro del objeto de propietario
    $result = $personas->map(function ($persona) {
        return [
            'id' => $persona->id,
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'ci' => $persona->ci,
            'telefono' => $persona->telefono,
            'persona_id' => $persona->id, // Incluye el ID de la persona
            'propietario' => $persona->propietario, // Información del propietario
        ];
    });

    return response()->json($result, 200);
}



public function buscarPorCI(Request $request)
{
    Log::info('CI recibido para buscar:', ['ci' => $request->input('ci')]);

    $request->validate([
        'ci' => 'required|string|max:20'
    ]);

    $ci = $request->input('ci');

    Log::info('Ejecutando consulta con CI:', ['ci' => $ci]);

    $persona = Persona::where('ci', $ci)->first();

    if ($persona) {
        Log::info('Persona encontrada:', $persona->toArray());
        return response()->json($persona, 200);
    }

    Log::warning('Persona no encontrada para CI:', ['ci' => $ci]);
    return response()->json(['message' => 'Persona no encontrada'], 404);
}




}
