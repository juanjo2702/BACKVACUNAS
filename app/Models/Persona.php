<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $fillable = ['nombres', 'apellidos', 'ci', 'telefono', 'usuario_id'];

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function miembro()
    {
        return $this->hasOne(Miembro::class, 'persona_id');
    }

    // Definir la relación con Propietario si aplica
    /* public function propietario()
    {
        return $this->hasOne(Propietario::class);
    } */
    public function propietario()
    {
        return $this->hasOne(Propietario::class, 'persona_id', 'id');
    }
    
}
