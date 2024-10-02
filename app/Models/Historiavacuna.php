<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historiavacuna extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'historiavacunas';

    // Permitir la asignación masiva en los siguientes campos
    protected $fillable = [
        'estado',           // Estado de vacunación (1 para vacunado, 0 para no vacunado)
        'motivo',           // Motivo de no vacunación (nullable)
        'mascota_id',       // ID de la mascota
        'participacion_id', // ID de la participación del miembro en la brigada
        'alcance_id'        // ID del alcance asociado a la zona de la brigada
    ];

    // Relación con la tabla Mascotas (una historia de vacunación pertenece a una mascota)
    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    // Relación con la tabla Participaciones (una historia de vacunación tiene una participación)
    public function participacion()
    {
        return $this->belongsTo(Participacion::class);
    }

    // Relación con la tabla Alcances (una historia de vacunación tiene un alcance)
    public function alcance()
    {
        return $this->belongsTo(Alcance::class);
    }
}
