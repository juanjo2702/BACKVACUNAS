<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Brigada;

class UsuarioController extends Controller
{
    public function login(Request $request)
{
    // Validar los datos recibidos
    $request->validate([
        'nombre' => 'required|string',
        'password' => 'required|string',
    ]);

    // Buscar el usuario por nombre
    $usuario = Usuario::where('nombre', $request->nombre)->first();

    // Verificar si el usuario existe y la contraseña es correcta
    if (!$usuario || !Hash::check($request->password, $usuario->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Credenciales incorrectas'
        ], 401);
    }

    // Iniciar sesión
    Auth::login($usuario);

    // Verificar si el usuario pertenece a una brigada
    $brigada = Brigada::where('usuario_id', $usuario->id)->first();

    // Si pertenece a una brigada, devolvemos un flag en la respuesta
    $isBrigada = $brigada ? true : false;

    return response()->json([
        'success' => true,
        'message' => 'Login exitoso',
        'user' => $usuario,
        'isBrigada' => $isBrigada  // Devuelve true si el usuario está en brigadas
    ]);
}


    // Método de logout (opcional, si se quiere implementar logout)
    public function logout(Request $request)
    {
        Auth::logout(); // Cierra la sesión del usuario

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

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
            'password' => Hash::make($validatedData['password']), // Encriptar la contraseña
            'estado' => $validatedData['estado'],
            'rol_id' => $validatedData['rol_id'],
        ]);

        // Devolver el ID del usuario en la respuesta
        return response()->json(['id' => $usuario->id], 201);
    }

    public function getJefesZona()
    {
        // Consultamos los usuarios con rol de jefe de zona (rol_id = 2)
        $jefesZona = DB::table('usuarios')
            ->join('personas', 'usuarios.id', '=', 'personas.usuario_id')
            ->where('usuarios.rol_id', 2)  // Rol 2 es Jefe de Zona
            ->select(DB::raw("CONCAT(personas.nombres, ' ', personas.apellidos) as nombreCompleto"), 'usuarios.id as usuario_id')
            ->get();

        return response()->json($jefesZona);
    }
}
