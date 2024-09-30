<?php

namespace App\Http\Controllers;

use App\Models\Brigada;
use App\Models\Usuario;
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
}
