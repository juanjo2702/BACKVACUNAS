<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $table = 'zonas';

    protected $fillable = [
        'centro',
        'cobertura',
        'municipio_id',
        'estado'
    ];

    // Relación: una zona puede tener muchas brigadas
    public function brigadas()
    {
        return $this->hasMany(Brigada::class);
    }

    // Relación con municipio (si existe)
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    // Relación con alcance (si la usas en otro contexto)
    public function alcances()
    {
        return $this->hasMany(Alcance::class);
    }
    public function campania()
    {
        return $this->belongsTo(Campania::class, 'campania_id');
    }
}
