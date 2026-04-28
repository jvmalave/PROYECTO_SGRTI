<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; // Se importa el Trait para UUID

class Estimation extends Model
{
    use HasUuid; // Se utiliza el Trait para UUID

    protected $table = 'requirements_core.estimations';

    protected $fillable = [
        'requirement_id',
        // Horas
        'h_analisis', 'h_diseno', 'h_construccion', 
        'h_pruebas', 'h_certificacion', 'h_implementacion',
        // Fechas Inicio
        'f_analisis_ini', 'f_diseno_ini', 'f_construccion_ini', 
        'f_prueba_ini', 'f_certificacion_ini', 'f_implementacion_ini',
        // Fechas Fin
        'f_analisis_fin', 'f_diseno_fin', 'f_construccion_fin', 
        'f_prueba_fin', 'f_certificacion_fin', 'f_implementacion_fin'
    ];

    // Casting de fechas: Laravel las tratará automáticamente como objetos Carbon (fechas reales)
    protected $casts = [
        'f_analisis_ini' => 'date', 'f_analisis_fin' => 'date',
        'f_diseno_ini'   => 'date', 'f_diseno_fin'   => 'date',
        'f_construccion_ini' => 'date', 'f_construccion_fin' => 'date',
        'f_prueba_ini'   => 'date', 'f_prueba_fin'   => 'date',
        'f_certificacion_ini' => 'date', 'f_certificacion_fin' => 'date',
        'f_implementacion_ini' => 'date', 'f_implementacion_fin' => 'date',
    ];

    // Relación inversa: Un registro de estimación pertenece a un Requerimiento
    public function requirement()
    {
        return $this->belongsTo(Requirement::class, 'requirement_id'); // Se define la relación inversa con el modelo Requirement
    }
}