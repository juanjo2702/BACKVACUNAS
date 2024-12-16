<?php

namespace App\Http\Controllers;

use App\Models\Brigada;
use App\Models\Usuario;
use App\Models\Miembro;
use App\Models\Zona;
use Illuminate\Http\Request;
use App\Models\Alcance;
use Illuminate\Support\Facades\Log;

class BrigadaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'campania_id' => 'required|exists:campanias,id',
            'zona_id' => 'required|exists:zonas,id',
            'usuario_id' => 'required|exists:usuarios,id',
        ]);

        // Crear nueva brigada
        $brigada = new Brigada();
        $brigada->campania_id = $request->campania_id; // Asociar con la campaña seleccionada
        $brigada->zona_id = $request->zona_id;
        $brigada->usuario_id = $request->usuario_id;
        $brigada->save();

        return response()->json(['message' => 'Brigada creada exitosamente.'], 201);
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
            $miembros = Miembro::whereHas('participacions', function ($query) use ($brigadaId) {
                $query->where('brigada_id', $brigadaId);
            })->with('persona')->get();

            return response()->json($miembros, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generarBrigadas(Request $request)
    {
        $request->validate([
            'zona_id' => 'required|exists:zonas,id',
            'campania_id' => 'required|exists:campanias,id',
            'num_brigadas' => 'required|integer|min:1',
        ]);

        $zonaId = $request->input('zona_id');
        $campaniaId = $request->input('campania_id');
        $numBrigadas = $request->input('num_brigadas');

        $zona = Zona::findOrFail($zonaId);

        $ultimoNumeroBrigada = Brigada::where('zona_id', $zonaId)
            ->where('campania_id', $campaniaId)
            ->count();

        $mesActual = now()->format('m');
        $anioActual = now()->year;

        $brigadasNuevas = [];
        for ($i = 1; $i <= $numBrigadas; $i++) {
            $numeroBrigada = $ultimoNumeroBrigada + $i;
            $username = "BR-{$numeroBrigada}-" . strtoupper($this->getInitialsFromCentro($zona->centro)) . "-{$mesActual}-{$anioActual}";
            $password = $this->generateRandomPassword(8);

            $usuario = Usuario::create([
                'nombre' => $username,
                'password' => bcrypt($password), // Almacenar cifrada
                'rol_id' => 1,
                'estado' => 1,
            ]);

            $brigada = Brigada::create([
                'usuario_id' => $usuario->id,
                'zona_id' => $zonaId,
                'campania_id' => $campaniaId,
            ]);

            $brigadasNuevas[] = [
                'nombre' => $username,
                'password' => $password, // Contraseña generada
            ];
        }

        $brigadasCompletas = Brigada::where('zona_id', $zonaId)
            ->where('campania_id', $campaniaId)
            ->with('usuario')
            ->get()
            ->map(function ($brigada) {
                return [
                    'nombre' => $brigada->usuario->nombre,
                    'password' => 'No Disponible (seguridad)', // Para no mostrar contraseñas antiguas
                ];
            });

        return response()->json([
            'message' => 'Brigadas generadas correctamente.',
            'brigadas' => $brigadasNuevas, // Mostrar las nuevas brigadas con sus contraseñas
            'brigadas_completas' => $brigadasCompletas,
        ]);
    }

    /**
     * Obtener las iniciales de la columna 'centro' (una letra por cada palabra).
     * Ejemplo: "Centro de Salud" => "CDS"
     */
    private function getInitialsFromCentro($centro)
    {
        $words = explode(' ', $centro);
        $initials = '';

        foreach ($words as $word) {
            $initials .= mb_substr($word, 0, 1); // Tomar la primera letra de cada palabra
        }

        return $initials;
    }

    /**
     * Generar una contraseña aleatoria alfanumérica de 8 caracteres.
     * Incluye letras mayúsculas, minúsculas y números.
     * @param int $length
     * @return string
     */
    private function generateRandomPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $maxIndex)];
        }

        return $password;
    }

    public function getZonasByCampania($id)
    {
        try {
            Log::info("Buscando zonas para la campaña ID: $id");
            $zonas = Alcance::where('campania_id', $id)->with('zona')->get();
            Log::info("Zonas encontradas: ", $zonas->toArray());
            return response()->json(['zonas' => $zonas], 200);
        } catch (\Exception $e) {
            Log::error("Error al obtener zonas: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getBrigadasByCampania($id)
    {
        $brigadas = Brigada::whereHas('zona', function ($query) use ($id) {
            $query->where('campania_id', $id);
        })->get();

        return response()->json(['brigadas' => $brigadas], 200);
    }

    public function getZonasPorCampania($campaniaId)
    {
        try {
            $zonas = Alcance::where('campania_id', $campaniaId)
                ->with('zona')
                ->get()
                ->map(function ($alcance) {
                    return [
                        'id' => $alcance->zona->id,
                        'nombre' => $alcance->zona->nombre,
                    ];
                });

            return response()->json(['zonas' => $zonas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las zonas.'], 500);
        }
    }
}
