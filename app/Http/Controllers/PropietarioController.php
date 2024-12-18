<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use App\Models\Persona;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PropietarioController extends Controller
{
    // Método para listar propietarios
    public function index()
    {
        // Fetch propietarios with related 'persona' data
        $propietarios = Propietario::with('persona')->get();

        // Modify the structure to include the full URL for the 'foto' field
        $propietariosConPersonas = $propietarios->map(function ($propietario) {
            return [
                'id' => $propietario->id,
                'direccion' => $propietario->direccion,
                'observaciones' => $propietario->observaciones,
                // Here is where you transform the 'foto' field to include the full asset URL
                'foto' => $propietario->foto ? asset('storage/' . $propietario->foto) : null,
                'latitud' => $propietario->latitud,
                'longitud' => $propietario->longitud,
                'persona' => [
                    'nombres' => $propietario->persona->nombres,
                    'apellidos' => $propietario->persona->apellidos,
                    'ci' => $propietario->persona->ci,
                    'telefono' => $propietario->persona->telefono,
                ]
            ];
        });

        // Return the modified data
        return response()->json($propietariosConPersonas);
        // Fetch propietarios with related 'persona' data
        $propietarios = Propietario::with('persona')->get();

        // Modify the structure to include the full URL for the 'foto' field
        $propietariosConPersonas = $propietarios->map(function ($propietario) {
            return [
                'id' => $propietario->id,
                'direccion' => $propietario->direccion,
                'observaciones' => $propietario->observaciones,
                // Here is where you transform the 'foto' field to include the full asset URL
                'foto' => $propietario->foto ? asset('storage/' . $propietario->foto) : null,
                'latitud' => $propietario->latitud,
                'longitud' => $propietario->longitud,
                'persona' => [
                    'nombres' => $propietario->persona->nombres,
                    'apellidos' => $propietario->persona->apellidos,
                    'ci' => $propietario->persona->ci,
                    'telefono' => $propietario->persona->telefono,
                ]
            ];
        });

        // Return the modified data
        return response()->json($propietariosConPersonas);
    }

    // Método para guardar un nuevo propietario
    public function store(Request $request)
    {
        // Validamos los datos requeridos
        // Validamos los datos requeridos
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'persona_id' => 'required|exists:personas,id',
            'direccion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // Validar el tipo de archivo
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // Validar el tipo de archivo
            'latitud' => 'required',
            'longitud' => 'required'
            'longitud' => 'required'
        ]);

        // Manejo de la imagen si es que se ha subido
        // Manejo de la imagen si es que se ha subido
        if ($request->hasFile('foto')) {
            // Guardamos la imagen en la carpeta 'public/images/propietarios'
            $path = $request->file('foto')->store('images/propietarios', 'public');
            // Guardamos la imagen en la carpeta 'public/images/propietarios'
            $path = $request->file('foto')->store('images/propietarios', 'public');
        } else {
            $path = null; // Si no se subió una foto, establecemos `null`
            $path = null; // Si no se subió una foto, establecemos `null`
        }

        // Creamos un nuevo registro de Propietario
        // Creamos un nuevo registro de Propietario
        $propietario = new Propietario();
        $propietario->direccion = $request->direccion;
        $propietario->observaciones = $request->observaciones;
        $propietario->foto = $path; // Guardamos la ruta de la imagen
        $propietario->foto = $path; // Guardamos la ruta de la imagen
        $propietario->latitud = $request->latitud;
        $propietario->longitud = $request->longitud;
        $propietario->persona_id = $request->persona_id;


        try {
            // Guardamos el propietario en la base de datos
            // Guardamos el propietario en la base de datos
            $propietario->save();
        } catch (\Illuminate\Database\QueryException $e) {
            // Si ocurre un error al guardar, devolvemos un error JSON
            return response()->json(['error' => 'Error al registrar el propietario.', 'message' => $e->getMessage()], 500);
            // Si ocurre un error al guardar, devolvemos un error JSON
            return response()->json(['error' => 'Error al registrar el propietario.', 'message' => $e->getMessage()], 500);
        }

        // Devolvemos una respuesta JSON indicando éxito
        // Devolvemos una respuesta JSON indicando éxito
        return response()->json(['message' => 'Propietario registrado correctamente', 'propietario' => $propietario], 201);
    }
    public function getPropietarioConMascotas($propietarioId)
    {
        $propietario = Propietario::with(['persona', 'mascotas'])->find($propietarioId);
        if ($propietario) {
            return response()->json([
                'nombres' => $propietario->persona->nombres,
                'apellidos' => $propietario->persona->apellidos,
                'mascotas' => $propietario->mascotas
            ]);
        } else {
            return response()->json(['error' => 'Propietario no encontrado'], 404);
        }
    }
    public function show($id)
    {
        $propietario = Propietario::with('persona')->findOrFail($id);
        dd($propietario); // Verifica si trae los datos de la persona
        return response()->json($propietario);
    }


    public function buscarPersonas(Request $request)
    {
        // Validar si hay un término de búsqueda
        $query = $request->get('q');

        if ($query) {
            // Buscar personas donde el nombre o apellido contenga el término de búsqueda
            $personas = Persona::where('nombres', 'LIKE', '%' . $query . '%')
                ->orWhere('apellidos', 'LIKE', '%' . $query . '%')
                ->get();
        } else {
            $personas = [];
        }

        return response()->json($personas);
    }
    public function obtenerMascotas($id)
    {
        // Encuentra al propietario y sus mascotas usando la relación definida en el modelo
        $persona = Persona::with('propietario')->find($id);
        $propietario = Propietario::with('mascotas')->find($persona->propietario->id);

        if (!$propietario) {
            return response()->json(['message' => 'Propietario no encontrado'], 404);
        }

        // Retorna las mascotas del propietario
        return response()->json($propietario->mascotas);
    }

    public function getPropietarioWithMascotas($propietario_id)
    {
        try {
            $propietario = Propietario::with('mascotas')->findOrFail($propietario_id);
            return response()->json($propietario, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo obtener el propietario con sus mascotas'], 500);
        }
    }

    public function showWithPerson($id)
    {
        try {
            // Obtiene el propietario con los datos relacionados de la persona
            $propietario = Propietario::with('persona')->find($id);

            if (!$propietario) {
                return response()->json(['error' => 'Propietario no encontrado'], 404);
            }

            return response()->json($propietario);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en el servidor: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        $validatedData = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'ci' => 'nullable|string|max:20|unique:personas,ci,' . $id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        try {
            // Actualizar los datos básicos de la persona
            $persona->update([
                'nombres' => $validatedData['nombres'],
                'apellidos' => $validatedData['apellidos'],
                'ci' => $validatedData['ci'],
                'telefono' => $validatedData['telefono'],
            ]);

            // Actualizar datos del propietario relacionado
            $propietario = $persona->propietario;
            if ($propietario) {
                $propietario->update([
                    'direccion' => $validatedData['direccion'],
                    'observaciones' => $validatedData['observaciones'],
                    'latitud' => $validatedData['latitud'],
                    'longitud' => $validatedData['longitud'],
                ]);
                if ($request->hasFile('foto')) {
                    Log::info('Imagen recibida:', ['nombre' => $request->file('foto')->getClientOriginalName()]);
                } else {
                    Log::warning('No se recibió ninguna imagen en la solicitud.');
                }

                if ($request->hasFile('foto')) {
                    // Elimina la imagen anterior, si existe
                    if ($propietario->foto && Storage::exists("public/{$propietario->foto}")) {
                        Storage::delete("public/{$propietario->foto}");
                    }

                    $path = $request->file('foto')->store('images/propietarios', 'public');
                    $propietario->foto = $path;
                } else {
                    // Si no hay nueva imagen, conserva la actual
                    $propietario->foto = $propietario->foto;
                }
                $propietario->save();
            }

            return response()->json(['message' => 'Persona y propietario actualizados correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar los datos', 'details' => $e->getMessage()], 500);
        }
    }


    public function obtenerPersonasConPropietario(Request $request)
    {
        $query = $request->get('q'); // Obtener el término de búsqueda

        $personas = Persona::with('propietario')
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where('nombres', 'LIKE', "%{$query}%")
                    ->orWhere('apellidos', 'LIKE', "%{$query}%")
                    ->orWhere('ci', 'LIKE', "%{$query}%")
                    ->orWhere('telefono', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json($personas);
    }
}
