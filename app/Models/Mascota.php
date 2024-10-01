<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Mascota extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'mascotas';

    // Campos asignables en masa
    protected $fillable = [
        'nombre',
        'genero',
        'especie',
        'rangoEdad',
        'color',
        'descripcion',
        'tamanio',
        'fotoFrontal',
        'fotoHorizontal',
        'estadoMascota',
        'estado',
        'raza_id',
        'propietario_id',
    ];

    // Relación con el modelo 'Propietario'
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'propietario_id');
    }

    // Relación con el modelo 'Raza'
    public function raza()
    {
        return $this->belongsTo(Raza::class, 'raza_id');
    }

    public function historiales()
    {
        return $this->hasMany(HistoriaVacuna::class, 'mascota_id');
    }
    // Accesor para obtener la URL completa de la imagen
    public function getFotoAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }
}
