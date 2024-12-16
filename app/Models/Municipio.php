<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'red_id',
    ];

    public function red()
    {
        return $this->belongsTo(Red::class);
    }

    public function zonas()
    {
        return $this->hasMany(Zona::class);
    }
}
