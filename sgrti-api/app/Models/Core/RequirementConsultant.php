<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; // Se importa el Trait para UUID

class RequirementConsultant extends Model
{
    use HasUuid;  // Se utiliza el Trait para UUID

    protected $table = 'requirements_core.requirement_consultants';

    protected $fillable = [
        'requirement_id', 
        'user_uuid', 
        'rol_cspe'
    ];

    // Relación inversa: Un registro de asignación pertenece a un Requerimiento
    public function requirement()
    {
        return $this->belongsTo(Requirement::class, 'requirement_id'); // Se define la relación inversa con el modelo Requirement
    }
}