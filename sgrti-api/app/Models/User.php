<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Interfaz necesaria para JWT
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements JWTSubject
{

    use HasFactory;
    use Notifiable, HasUuid;

    protected $table = 'identity.users';


    protected $fillable = [
      'name',
      'email',
      'password',
      'roles', 
  ];

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
        'roles' => 'array', // Se asegura que el campo roles se maneje como un array (JSONB)
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
            'roles' => $this->roles, // Se Inyecta los roles en el payload del token (US03)
            'name'  => $this->name
        ];
    }
}

