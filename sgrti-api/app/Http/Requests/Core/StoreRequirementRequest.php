<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequirementRequest extends FormRequest
{
    /**
     * Determina si el usuario tiene permiso para esta petición.
     * Por ahora lo dejamos en true (la seguridad JWT se encarga después).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para el Momento 1
     */
    public function rules(): array
    {
        return [
            // Requerimiento (Padre)
            
            'numero_rrti' => 'required|string|unique:pgsql.requirements_core.requirements,numero_rrti',
            'tipo_requerimiento'    => 'required|string',
            'anio'                  => 'required|integer|min:2020',
            'fecha_creacion'        => 'required|date',
            'descripcion_detallada' => 'required|string|min:10',
            
            // Unidad Solicitante (Anidado)
            'requesting_unit'                           => 'required|array',
            'requesting_unit.sociedad'                  => 'required|string',
            'requesting_unit.sistema'                   => 'required|string',
            'requesting_unit.unidad_solicitante'        => 'required|string',
            'requesting_unit.contacto_funcional_nom'    => 'required|string',
            'requesting_unit.contacto_funcional_telf'   => 'required|string',
            'requesting_unit.contacto_funcional_correo' => 'required|email',
            'requesting_unit.contacto_gpgti_nom'        => 'required|string',
            'requesting_unit.contacto_gpgti_correo'     => 'required|email',

            // Consultores CSPE
            'consultants_uuids'     => 'required|array|min:1',
            //'consultants_uuids.*'   => 'required|uuid'
        ];
    }

    /**
     * Mensajes personalizados en español (Opcional pero recomendado para el usuario final)
     */
    public function messages(): array
    {
        return [
            'numero_rrti.unique' => 'El número de RRTI ya se encuentra registrado en el sistema.',
            'requesting_unit.contacto_funcional_correo.email' => 'El formato del correo del consultor funcional no es válido.',
            'requesting_unit.contacto_gpgti_correo.email' => 'El formato del correo del consultor GPGTI no es válido.',
            'consultants_uuids.min' => 'Debe asignar al menos un consultor responsable al requerimiento.'
        ];
    }
}