<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index()
    {
        return Usuario::all();
    }

    /**
     * Guardar un nuevo usuario.
     */
    public function store(Request $request)
    {
        // Validaciones
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'password' => 'required|string',
            'estado' => 'required',
            'rol_id' => 'required|exists:rols,id', // Asegurarse de que el rol exista
        ]);

        // Crear el usuario
        $usuario = Usuario::create([
            'nombre' => $validatedData['nombre'],
            'password' => bcrypt($validatedData['password']), // Encriptar la contraseña
            'estado' => $validatedData['estado'],
            'rol_id' => $validatedData['rol_id'],
        ]);

        // Devolver el ID del usuario en la respuesta
        return response()->json(['id' => $usuario->id], 201);
    }

    public function getJefesZona() {
        // Consultamos los usuarios con rol de jefe de zona (rol_id = 2)
        $jefesZona = DB::table('usuarios')
            ->join('personas', 'usuarios.id', '=', 'personas.usuario_id')
            ->where('usuarios.rol_id', 2)  // Rol 2 es Jefe de Zona
            ->select(DB::raw("CONCAT(personas.nombres, ' ', personas.apellidos) as nombreCompleto"), 'usuarios.id as usuario_id')
            ->get();

        return response()->json($jefesZona);
    }

}
