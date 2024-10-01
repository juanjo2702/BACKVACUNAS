<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participacion extends Model
{
    use HasFactory;

    // Definir la tabla asociada
    protected $table = 'participacions';

    // Atributos que pueden ser asignados masivamente
    protected $fillable = [
        'miembro_id',
        'brigada_id',
    ];

    // Relación con el modelo Miembro
    public function miembro()
    {
        return $this->belongsTo(Miembro::class, 'miembro_id');
    }

    // Relación con el modelo Brigada
    public function brigada()
    {
        return $this->belongsTo(Brigada::class, 'brigada_id');
    }
}
