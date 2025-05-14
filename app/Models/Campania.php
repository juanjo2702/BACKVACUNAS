<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campania extends Model
{
    use HasFactory;

    // Define la tabla asociada (opcional si la tabla sigue el estándar plural)
    protected $table = 'campanias';

    // Define los campos que pueden ser asignados masivamente
    protected $fillable = [
        'nombre',
        'fechaInicio',
        'fechaFinal',
        'estado',
    ];

    // Si tu tabla tiene las columnas created_at y updated_at (timestamps)
    public $timestamps = true;

    public function brigadas()
    {
        return $this->hasMany(Brigada::class, 'campania_id');
    }
}
