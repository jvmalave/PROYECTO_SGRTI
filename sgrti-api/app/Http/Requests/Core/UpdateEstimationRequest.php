<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstimationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La seguridad por Token se valida en el middleware
    }

    public function rules(): array
    {
        return [
            // Horas (deben ser numéricas y mínimo 0)
            'h_analisis'       => 'nullable|numeric|min:0',
            'h_diseno'         => 'nullable|numeric|min:0',
            'h_construccion'   => 'nullable|numeric|min:0',
            'h_pruebas'        => 'nullable|numeric|min:0',
            'h_certificacion'  => 'nullable|numeric|min:0',
            'h_implementacion' => 'nullable|numeric|min:0',

            // Fechas de Inicio
            'f_analisis_ini'       => 'nullable|date',
            'f_diseno_ini'         => 'nullable|date',
            'f_construccion_ini'   => 'nullable|date',
            'f_prueba_ini'         => 'nullable|date',
            'f_certificacion_ini'  => 'nullable|date',
            'f_implementacion_ini' => 'nullable|date',

            // Fechas de Fin (validadas contra sus inicios)
            'f_analisis_fin'       => 'nullable|date|after_or_equal:f_analisis_ini',
            'f_diseno_fin'         => 'nullable|date|after_or_equal:f_diseno_ini',
            'f_construccion_fin'   => 'nullable|date|after_or_equal:f_construccion_ini',
            'f_prueba_fin'         => 'nullable|date|after_or_equal:f_prueba_ini',
            'f_certificacion_fin'  => 'nullable|date|after_or_equal:f_certificacion_ini',
            'f_implementacion_fin' => 'nullable|date|after_or_equal:f_implementacion_ini',
        ];
    }

    public function messages(): array
    {
        return [
            '*.after_or_equal' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.',
            '*.numeric'        => 'El campo de horas debe ser un número válido.',
        ];
    }
}