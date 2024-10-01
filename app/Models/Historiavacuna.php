<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historiavacuna extends Model
{
    use HasFactory;

    protected $table = 'historiavacunas';

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'mascota_id');
    }
}
