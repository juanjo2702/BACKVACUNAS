<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    // Definir la tabla correspondiente en la base de datos (opcional si sigue las convenciones de Laravel)
    protected $table = 'zonas';

    // Definir los campos que pueden ser rellenados en masa (mass-assignment)
    protected $fillable = [
        'nombre',
        'centro',
        'ciudad',
        'departamento',
        'estado'
    ];

    public function brigadas()
    {
        return $this->hasMany(Brigada::class);
    }

    // Relación con alcances
    public function alcances()
    {
        return $this->hasMany(Alcance::class);
    }
    public function campania()
    {
        return $this->belongsTo(Campania::class, 'campania_id');
    }
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
