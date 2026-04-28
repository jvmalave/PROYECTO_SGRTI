<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; // Se importa el Trait

class RequestingUnit extends Model
{
    use HasUuid; // Se utiliza el Trait para UUID

    protected $table = 'requirements_core.requesting_units';

    protected $fillable = [
        'requirement_id', 'sociedad', 'sistema', 'unidad_solicitante',
        'contacto_funcional_nom', 'contacto_funcional_telf', 'contacto_funcional_correo',
        'contacto_gpgti_nom', 'contacto_gpgti_correo'
    ];
    
     // Relación Inversa: La Unidad Solicitante pertenece a un Requerimiento.
    public function requirement()
    {
        return $this->belongsTo(Requirement::class, 'requirement_id'); // Se define la relación inversa con el modelo Requirement
    }
}


