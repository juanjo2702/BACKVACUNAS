<?php

namespace App\Http\Controllers;

use App\Models\Brigada;
use App\Models\Usuario;
use App\Models\Miembro;
use Illuminate\Http\Request;

class BrigadaController extends Controller
{
    public function store(Request $request)
    {
        // Crear nuevo usuario con rol de brigada
        $usuario = new Usuario();
        $usuario->nombre = $request->input('usuario.nombre');
        $usuario->password = bcrypt($request->input('usuario.password'));
        $usuario->rol_id = $request->input('usuario.rol_id');
        $usuario->estado = $request->input('usuario.estado');
        $usuario->save();

        // Crear brigada con el usuario y zona correspondiente
        $brigada = new Brigada();
        $brigada->usuario_id = $usuario->id;
        $brigada->zona_id = $request->input('zona_id');
        $brigada->save();

        return response()->json(['message' => 'Brigada creada exitosamente']);
    }

    public function getBrigadaByUsuario($usuarioId)
    {
        // Busca si existe una brigada con el usuario_id proporcionado
        $brigada = Brigada::where('usuario_id', $usuarioId)->first();

        if ($brigada) {
            // Si existe, devolver el id de la brigada
            return response()->json([
                'exists' => true,
                'brigada_id' => $brigada->id
            ], 200);
        } else {
            // Si no existe, devolver que no está en brigadas
            return response()->json([
                'exists' => false
            ], 200);
        }
    }
    public function getMiembrosBrigada($brigadaId)
    {
        try {
            // Buscar la brigada por su ID
            $brigada = Brigada::findOrFail($brigadaId);

            // Usar la relación 'participacions' y la relación con la tabla 'persona'
            $miembros = Miembro::whereHas('participacions', function($query) use ($brigadaId) {
                $query->where('brigada_id', $brigadaId);
            })->with('persona')->get();

            return response()->json($miembros, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
