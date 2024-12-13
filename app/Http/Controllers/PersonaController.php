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
    // Método para listar personas
    public function index(Request $request)
    {
        if ($request->has('filter') && $request->filter === 'withUsuario') {
            // Traer personas con usuario relacionado y rol_id = 2 (jefes de zona)
            $personas = Persona::with(['usuario' => function ($query) {
                $query->where('rol_id', 2);
            }])->whereHas('usuario', function ($query) {
                $query->where('rol_id', 2);
            })->get();

            return response()->json($personas);
        }

        // Si no hay filtro, retornar todas las personas
        $personas = Persona::all();
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

            $persona->usuario_id = $validated['usuario_id'] ?? null; // Asignar el usuario_id si se envía

            $persona->save();

            return response()->json(['message' => 'Persona registrada correctamente', 'persona' => $persona, 'id' => $persona->id], 201); // Código 201 Creado
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
        $persona = Persona::with('usuario')->find($id); // Incluye la relación con el usuario

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
        $personas = Persona::where('estado', 1) // Filtra solo personas con estado 1
            ->with('propietario') // Incluye los datos del propietario
            ->get();
        // Obtiene las personas junto con los datos del propietario asociado
        $personas = Persona::with('propietario')->get();

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

        return response()->json($result);
    }

    public function buscarPorCI(Request $request)
{
    $ci = $request->input('ci'); // Recuperar el CI desde el cuerpo de la solicitud
    $persona = DB::table('personas')->where('ci', $ci)->first();

    if ($persona) {
        return response()->json($persona, 200);
    }

    return response()->json(['error' => 'Persona no encontrada'], 404);
}

    public function updateJefeZona(Request $request, $id)
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        // Validar los datos enviados
        $validatedData = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'ci' => 'nullable|string|max:20|unique:personas,ci,' . $id,
            'telefono' => 'nullable|string|max:20',
            'usuario.nombre' => 'required|string|max:255', // Nombre de usuario obligatorio
            'usuario.password' => 'nullable|string|min:6', // Contraseña opcional
        ]);

        try {
            // Actualizar los datos de la persona
            $persona->update([
                'nombres' => $validatedData['nombres'],
                'apellidos' => $validatedData['apellidos'],
                'ci' => $validatedData['ci'],
                'telefono' => $validatedData['telefono'],
            ]);

            // Actualizar los datos del usuario relacionado
            if ($persona->usuario) {
                $persona->usuario->update([
                    'nombre' => $validatedData['usuario']['nombre'],
                    'password' => isset($validatedData['usuario']['password'])
                        ? bcrypt($validatedData['usuario']['password']) // Encripta la contraseña si se envía
                        : $persona->usuario->password, // Mantener la contraseña actual si no se envía una nueva
                ]);
            }

            return response()->json(['message' => 'Jefe de Zona actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar los datos', 'details' => $e->getMessage()], 500);
        }
    }
}