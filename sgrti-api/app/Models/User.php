<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Interfaz necesaria para JWT

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasUuid;

    protected $table = 'identity.users';

    /**
 * Los atributos que deben ser casteados.
 *
 * @return array<string, string>
 */
    protected function casts(): array
  {
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'roles' => 'array', // Aseguramos que los roles se manejen como un array
    ];
  }

    // Métodos obligatorios de la interfaz JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Retorna el UUID
    }

    public function getJWTCustomClaims()
    {
        return [
            'roles' => $this->roles, // Inyectamos los roles en el payload del token (US03)
            'name'  => $this->name
        ];
    }
}