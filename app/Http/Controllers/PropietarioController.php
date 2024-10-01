<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use Illuminate\Http\Request;

class PropietarioController extends Controller
{
    // Método para listar propietarios
    public function index()
    {
        // Utilizamos la relación 'persona' para obtener los datos de la persona asociada
        $propietarios = Propietario::with('persona')->get();

        // Opcionalmente, puedes modificar la estructura de los datos si deseas solo campos específicos
        $propietariosConPersonas = $propietarios->map(function ($propietario) {
            return [
                'id' => $propietario->id,
                'direccion' => $propietario->direccion,
                'observaciones' => $propietario->observaciones,
                'foto' => $propietario->foto ? asset('storage/' . $propietario->foto) : null, // Devolver la URL completa de la foto
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

        return response()->json($propietariosConPersonas);
    }

    // Método para guardar un nuevo propietario
    public function store(Request $request)
    {
        if (!$request->persona_id) {
            return response()->json(['error' => 'El ID de la persona es requerido.'], 400);
        }
        $request->validate([
            'direccion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validar el tipo de archivo
            'latitud' => 'required',
            'longitud' => 'required',
            'persona_id' => 'required|exists:personas,id'

        ]);




        // Manejar la imagen
    if ($request->hasFile('foto')) {
        // Almacenar la imagen en 'images/propietarios' dentro de 'public'
        $path = $request->file('foto')->store('images/propietarios', 'public');
    } else {
        $path = null; // Si no se subió una foto
    }

        $propietario = new Propietario();
        $propietario->direccion = $request->direccion;
        $propietario->observaciones = $request->observaciones;
        $propietario->foto = $path; // Guardar la ruta de la foto
        $propietario->latitud = $request->latitud;
        $propietario->longitud = $request->longitud;
        $propietario->persona_id = $request->persona_id;
        try {
            $propietario->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Error al registrar el propietario. Verifica que persona_id es válido.', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Propietario registrado correctamente', 'propietario' => $propietario], 201);
    }
}
