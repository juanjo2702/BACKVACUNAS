<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Http\Requests\StoreMascotaRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateMascotaRequest;
use App\Models\HistoriaVacuna;
use App\Models\Alcance;
use App\Models\Campania;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MascotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las mascotas junto con sus relaciones (propietario y raza)
            $mascotas = Mascota::with(['propietario', 'raza'])->get();
            return response()->json($mascotas, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la lista de mascotas',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos de la mascota
        $request->validate([
            'nombre' => 'required|string|max:255',
            'genero' => 'required|string|max:10',
            'especie' => 'required|string|max:50',
            'rangoEdad' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'tamanio' => 'required|string|max:50',
            'fotoFrontal' => 'nullable|image|mimes:jpeg,png,jpg',
            'fotoLateral' => 'nullable|image|mimes:jpeg,png,jpg',
            'estadoMascota' => 'nullable|string|max:50',
            'estado' => 'nullable|boolean',
            'raza_id' => 'required|exists:razas,id',
            'propietario_id' => 'required|exists:propietarios,id'
        ]);

        try {
            // Crear una nueva mascota
            $mascotaData = $request->all();

            // Manejar la subida de la imagen frontal
            if ($request->hasFile('fotoFrontal')) {
                $frontalPath = $request->file('fotoFrontal')->store('images/mascotas', 'public');
                $mascotaData['fotoFrontal'] = $frontalPath; // Guardar la ruta de la imagen frontal
            }

            // Manejar la subida de la imagen lateral
            if ($request->hasFile('fotoLateral')) {
                $lateralPath = $request->file('fotoLateral')->store('images/mascotas', 'public');
                $mascotaData['fotoLateral'] = $lateralPath; // Guardar la ruta de la imagen lateral
            }

            // Crear la mascota con los datos proporcionadosssssssssssss
            $mascota = Mascota::create($mascotaData);

            return response()->json([
                'message' => 'Mascota registrada con éxito',
                'mascota' => $mascota
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al registrar la mascota',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function obtenerPorPropietario($id)
    {
        $mascotas = Mascota::where('propietario_id', $id)->get();
        return response()->json($mascotas);
    }
    public function obtenerMascotasPorPropietario($id)
    {
        $mascotas = Mascota::where('propietario_id', $id)->get();

        // Asegurarnos de devolver la URL completa de la imagen frontal
        foreach ($mascotas as $mascota) {
            $mascota->foto_frontal_url = $mascota->fotoFrontal
                ? asset('storage/' . $mascota->fotoFrontal)
                : asset('images/placeholder.png'); // Imagen por defecto si no hay foto
        }

        return response()->json($mascotas);
    }
    public function show($id)
    {
        $mascota = Mascota::with('propietario')->find($id);
        return response()->json($mascota);
    }
    public function getByPropietario($propietarioId)
    {
        $mascotas = Mascota::where('propietario_id', $propietarioId)->get();
        return response()->json($mascotas);
    }
    public function obtenerRazaPorMascota($id)
    {
        // Obtener la mascota junto con la relación de raza
        $mascota = Mascota::with('raza')->find($id);

        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }

        // Si la mascota tiene una raza, obtenemos el nombre de la raza
        $raza = $mascota->raza ? $mascota->raza->nombre : 'Sin raza';

        // Retornamos el nombre de la raza
        return response()->json([
            'raza' => $raza
        ]);
    }

    public function obtenerHistorialVacunasPorMascota($mascotaId)
    {
        // Obtener los tres últimos registros de la tabla historiavacunas para la mascota
        $historialVacunas = Historiavacuna::where('mascota_id', $mascotaId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $vacunas = [];

        foreach ($historialVacunas as $registro) {
            // Obtener el estado de vacunación y motivo si no está vacunado
            $vacuna = [
                'estado' => $registro->estado,
                'motivo' => $registro->estado == 0 ? $registro->motivo : null, // Solo si no está vacunado
                'fecha' => $registro->created_at
            ];

            // Obtener el alcance y campaña
            $alcance = Alcance::find($registro->alcance_id);
            if ($alcance) {
                $campania = Campania::find($alcance->campania_id);
                if ($campania) {
                    $vacuna['campania_nombre'] = $campania->nombre;
                    $vacuna['campania_fecha_fin'] = $campania->fechaFinal;
                }
            }

            $vacunas[] = $vacuna;
        }

        return response()->json($vacunas);
    }

    public function getMascotasByPropietario($propietario_id)
    {
        try {
            $mascotas = Mascota::where('propietario_id', $propietario_id)->get();


            return response()->json($mascotas, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudieron obtener las mascotas'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $mascota = Mascota::find($id);

        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'genero' => 'required|string|in:Macho,Hembra',
            'especie' => 'required|string|in:Perro,Gato',
            'raza_id' => 'required|exists:razas,id',
            'color' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:1000',
            'tamanio' => 'required|string|in:Pequeño,Mediano,Grande',
            'rangoEdad' => 'required|date_format:Y-m-d',
            'fotoFrontal' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'fotoHorizontal' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        try {
            // Actualizar los datos básicos de la mascota
            $mascota->update([
                'nombre' => $validatedData['nombre'],
                'genero' => $validatedData['genero'],
                'especie' => $validatedData['especie'],
                'raza_id' => $validatedData['raza_id'],
                'color' => $validatedData['color'],
                'descripcion' => $validatedData['descripcion'],
                'tamanio' => $validatedData['tamanio'],
                'rangoEdad' => $validatedData['rangoEdad'], // Fecha calculada
            ]);

            // Manejo de imágenes (foto frontal)
            if ($request->hasFile('fotoFrontal')) {
                if ($mascota->fotoFrontal && Storage::exists("public/{$mascota->fotoFrontal}")) {
                    Storage::delete("public/{$mascota->fotoFrontal}");
                }

                $pathFrontal = $request->file('fotoFrontal')->store('images/mascotas', 'public');
                $mascota->fotoFrontal = $pathFrontal;
            }

            // Manejo de imágenes (foto lateral)
            if ($request->hasFile('fotoHorizontal')) {
                if ($mascota->fotoHorizontal && Storage::exists("public/{$mascota->fotoHorizontal}")) {
                    Storage::delete("public/{$mascota->fotoHorizontal}");
                }

                $pathHorizontal = $request->file('fotoHorizontal')->store('images/mascotas', 'public');
                $mascota->fotoHorizontal = $pathHorizontal;
            }

            // Guardar cambios en la mascota
            $mascota->save();

            return response()->json(['message' => 'Mascota actualizada correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la mascota', 'details' => $e->getMessage()], 500);
        }
    }

    public function mascotasCercanas(Request $request)
    {
        try {
            $lat = $request->input('latitud');
            $lng = $request->input('longitud');
            $radio_km = max((float)$request->input('radio', 1), 1); // mínimo 1 km

            $especie = $request->input('especie');
            $raza = $request->input('raza');
            $genero = $request->input('genero');

            $query = DB::table('mascotas')
                ->join('propietarios', 'mascotas.propietario_id', '=', 'propietarios.id')
                ->join('personas', 'propietarios.persona_id', '=', 'personas.id')
                ->leftJoin('razas', 'razas.id', '=', 'mascotas.raza_id')
                ->leftJoin('historiavacunas', 'historiavacunas.mascota_id', '=', 'mascotas.id')
                ->leftJoin('alcances', 'alcances.id', '=', 'historiavacunas.alcance_id')
                ->leftJoin('campanias', 'campanias.id', '=', 'alcances.campania_id')
                ->select(
                    'mascotas.id',
                    'mascotas.nombre as nombre_mascota',
                    'mascotas.fotoFrontal',
                    'razas.nombre as raza',
                    'mascotas.especie',
                    'mascotas.genero',
                    'personas.nombres',
                    'personas.apellidos',
                    'personas.telefono',
                    DB::raw('IF(campanias.id IS NOT NULL, "Vacunado", "No vacunado") as estado_vacunacion'),
                    'propietarios.latitud',
                    'propietarios.longitud'
                )
                ->whereRaw("ST_Distance_Sphere(POINT(?, ?), POINT(propietarios.longitud, propietarios.latitud)) <= ?", [
                    $lng,
                    $lat,
                    $radio_km * 1000
                ])
                ->where(function ($query) {
                    $query->whereNull('campanias.id')
                        ->orWhere('campanias.id', function ($subquery) {
                            $subquery->select('id')
                                ->from('campanias')
                                ->orderByDesc('fechaFinal')
                                ->limit(1);
                        });
                });

            // Filtros dinámicos
            if (!empty($especie)) {
                $query->where('mascotas.especie', $especie);
            }

            if (!empty($raza)) {
                $query->where('razas.nombre', $raza); // ahora se filtra por nombre de raza
            }

            if (!empty($genero)) {
                $query->where('mascotas.genero', $genero);
            }

            $resultados = $query->get();
            return response()->json($resultados);
        } catch (\Throwable $e) {
            Log::error('Error en búsqueda geográfica: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}




/* public function desactivar($id)
{
    try {
        // Buscar la mascota
        $mascota = Mascota::findOrFail($id);

        // Cambiar el estado a 0 (desactivado)
        $mascota->estado = 0;
        $mascota->save();

        return response()->json([
            'message' => 'La mascota ha sido desactivada correctamente.',
            'mascota' => $mascota
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'No se pudo desactivar la mascota',
            'message' => $e->getMessage(),
        ], 500);
    }
} */
