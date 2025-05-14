<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model  implements AuthenticatableContract
{
    use HasFactory;
    use Authenticatable;
    protected $table = 'usuarios';  // Si el nombre de la tabla es diferente a "users"

    // Definir los campos que se pueden rellenar de forma masiva
    protected $fillable = [
        'nombre',  // Nombre de usuario
        'password',
        'estado',
        'rol_id',
    ];

    // Ocultar los campos de contraseña cuando se realicen consultas
    protected $hidden = [
        'password',
    ];

    // Relación uno a uno con la tabla "Personas"
    public function persona()
    {
        return $this->hasOne(Persona::class, 'usuario_id', 'id');
    }

    // Relación con brigadas (un usuario puede estar en una brigada)

    public function brigada()
    {
        return $this->hasOne(Brigada::class, 'usuario_id');
    }
}
