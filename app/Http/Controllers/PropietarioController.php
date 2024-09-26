<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use Illuminate\Http\Request;

class PropietarioController extends Controller
{
    // Método para listar propietarios
    public function index()
    {
        $propietarios = Propietario::with('persona')->get(); // Asumiendo que hay una relación con Persona
        return response()->json($propietarios);
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
            'latitud' => 'required',
            'longitud' => 'required',
            'persona_id' => 'required|exists:personas,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Validar el tipo de archivo
        ]);

        // Manejar la imagen
        if ($request->hasFile('foto')) {
            $path = $request->hasFile('foto') ? $request->file('foto')->store('fotos', 'public') : null; // Almacenar en el disco 'public' (puedes cambiar el disco según tu configuración)
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
