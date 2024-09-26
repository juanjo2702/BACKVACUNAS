<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raza extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'razas';

    // Campos asignables en masa
    protected $fillable = [
        'nombre'
    ];

    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'raza_id');
    }
}
