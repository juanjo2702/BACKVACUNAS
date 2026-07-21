<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alcance extends Model
{
    use HasFactory;

    protected $fillable = ['campania_id', 'zona_id', 'persona_id']; // Persona representa al Jefe de Zona

    // Relación con la campaña
    public function campania()
    {
        return $this->belongsTo(Campania::class);
    }

    // Relación con la zona
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    // Relación con la persona que es jefe de zona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function miembros()
    {
        return $this->hasMany(Miembro::class);
    }
}
