<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Http\Requests\StoreMascotaRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateMascotaRequest;
use App\Models\HistoriaVacuna;
use App\Models\Alcance;
use App\Models\Campania;

class MascotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las mascotas junto con sus relaciones (propietario y raza)
            $mascotas = Mascota::with(['propietarios', 'razas'])->get();
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

            // Crear la mascota con los datos proporcionados
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
}
