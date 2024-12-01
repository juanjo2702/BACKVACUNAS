<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    use HasFactory;

    protected $fillable = ['direccion', 'observaciones', 'latitud', 'longitud', 'persona_id', 'foto'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
    // Definir la relación con Propietario si aplica
    // Definir la relación con Mascotas
    public function mascotas()
    {
        return $this->hasMany(Mascota::class);
    }


}
