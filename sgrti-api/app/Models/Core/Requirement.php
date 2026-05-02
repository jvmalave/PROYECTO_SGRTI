<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid; // Se importa el Trait
use App\Models\User; // Se importa el modelo User para la relación con consultores

class Requirement extends Model
{
    use HasUuid, SoftDeletes; //   Se utiliza el Trait para UUIDT y el SoftDeletes borrado lógico

    protected $appends = ['is_editable'];

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

    // public function consultants()
    // {
    //     return $this->hasMany(RequirementConsultant::class, 'requirement_id');
    // }

    public function consultants()
{
    return $this->belongsToMany(
        User::class, 
        'requirements_core.requirement_consultants', // Tabla pivote con esquema
        'requirement_id', 
        'user_uuid', 
        'id', 
        'id'
    )->withPivot('rol_cspe'); 
}

    public function getIsEditableAttribute(): bool
    {
        // Solo es editable si NO ha llegado a ETF
        return $this->fase_actual !== 'ATF';
    }
}