<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    use HasFactory;

    protected $table = 'miembros';

    // Atributos que pueden ser asignados masivamente
    protected $fillable = [
        'persona_id',
        'fotoAnverso',
        'fotoReverso',
        'estado',
    ];

    // Relación con el modelo Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    // Relación con el modelo Participacion
    public function participaciones()
    {
        return $this->hasMany(Participacion::class, 'miembro_id');
    }

}
