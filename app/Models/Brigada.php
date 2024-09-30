<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brigada extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'zona_id', 'estado'];

    // Relación con el usuario (quien será miembro de la brigada)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // Relación con la zona
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
