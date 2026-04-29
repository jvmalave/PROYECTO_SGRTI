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
    $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
    $requirementId = $this->route('id'); // Captura el UUID de la URL

    return [
        // Requerimiento (Padre)
        'numero_rrti' => [
            $isUpdate ? 'sometimes' : 'required',
            'string',
            // Si es update, ignora el registro actual para que no falle el unique
            $isUpdate 
                ? "unique:pgsql.requirements_core.requirements,numero_rrti,{$requirementId}" 
                : "unique:pgsql.requirements_core.requirements,numero_rrti"
        ],
        'tipo_requerimiento'    => [$isUpdate ? 'sometimes' : 'required', 'string'],
        'anio'                  => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:2020'],
        'fecha_creacion'        => [$isUpdate ? 'sometimes' : 'required', 'date'],
        'descripcion_detallada' => [$isUpdate ? 'sometimes' : 'required', 'string', 'min:10'],
        
        // Unidad Solicitante (Anidado)
        'requesting_unit'                           => [$isUpdate ? 'sometimes' : 'required', 'array'],
        'requesting_unit.sociedad'                  => 'required_with:requesting_unit|string',
        'requesting_unit.sistema'                   => 'required_with:requesting_unit|string',
        'requesting_unit.unidad_solicitante'        => 'required_with:requesting_unit|string',
        'requesting_unit.contacto_funcional_nom'    => 'required_with:requesting_unit|string',
        'requesting_unit.contacto_funcional_telf'   => 'required_with:requesting_unit|string',
        'requesting_unit.contacto_funcional_correo' => 'required_with:requesting_unit|email',
        'requesting_unit.contacto_gpgti_nom'        => 'required_with:requesting_unit|string',
        'requesting_unit.contacto_gpgti_correo'     => 'required_with:requesting_unit|email',

        // Consultores CSPE
        'consultants_uuids'     => [$isUpdate ? 'sometimes' : 'required', 'array', 'min:1'],
        'consultants_uuids.*'   => 'required_with:consultants_uuids|uuid',
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