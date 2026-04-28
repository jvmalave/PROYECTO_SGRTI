<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid; // Se importa el Trait

class Requirement extends Model
{
    use HasUuid, SoftDeletes; //   Se utiliza el Trait para UUIDT y el SoftDeletes borrado lógico

    protected $table = 'requirements_core.requirements';

    // 
    protected $fillable = [
        'numero_rrti', 'tipo_requerimiento', 'anio', 
        'fecha_creacion', 'doc_solicitud_ti', 'planilla_necesidades', 
        'descripcion_detallada', 'fase_actual', 'estado_interno'
    ];

    // --- RELACIONES ---

    public function requestingUnit()
    {
        return $this->hasOne(RequestingUnit::class, 'requirement_id');
    }

    public function estimation()
    {
        return $this->hasOne(Estimation::class, 'requirement_id');
    }

    public function consultants()
    {
        return $this->hasMany(RequirementConsultant::class, 'requirement_id');
    }
}