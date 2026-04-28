<?php

namespace App\Services\Core;

use App\Models\Core\Requirement;
use Illuminate\Support\Facades\DB;
use Exception;

class RequirementService
{
    /**
     * Proceso del Momento 1: Registro Inicial
     * Responsabilidad: Coordinador
     */
    public function createInitialRequirement(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // 1. Crear el Requerimiento (Padre)
                $requirement = Requirement::create([
                    'numero_rrti'           => $data['numero_rrti'],
                    'tipo_requerimiento'    => $data['tipo_requerimiento'],
                    'anio'                  => $data['anio'],
                    'fecha_creacion'        => $data['fecha_creacion'],
                    'descripcion_detallada' => $data['descripcion_detallada'],
                    'doc_solicitud_ti'      => $data['doc_solicitud_ti'] ?? null,
                    'planilla_necesidades'  => $data['planilla_necesidades'] ?? null,
                    'fase_actual'           => 'PL',
                    'estado_interno'        => 'Abierto'
                ]);

                // 2. Crear la Unidad Solicitante vinculada
                // Usamos la relación definida en el modelo
                $requirement->requestingUnit()->create([
                    'sociedad'                  => $data['requesting_unit']['sociedad'],
                    'sistema'                   => $data['requesting_unit']['sistema'],
                    'unidad_solicitante'        => $data['requesting_unit']['unidad_solicitante'],
                    'contacto_funcional_nom'    => $data['requesting_unit']['contacto_funcional_nom'],
                    'contacto_funcional_telf'   => $data['requesting_unit']['contacto_funcional_telf'],
                    'contacto_funcional_correo' => $data['requesting_unit']['contacto_funcional_correo'],
                    'contacto_gpgti_nom'        => $data['requesting_unit']['contacto_gpgti_nom'],
                    'contacto_gpgti_correo'     => $data['requesting_unit']['contacto_gpgti_correo'],
                ]);

                // 3. Asignar Consultores Responsables
                if (!empty($data['consultants_uuids'])) {
                    foreach ($data['consultants_uuids'] as $userUuid) {
                        $requirement->consultants()->create([
                            'user_uuid' => $userUuid,
                            'rol_cspe'  => 'Responsable'
                        ]);
                    }
                }

                // Cargamos las relaciones para devolver el objeto completo
                return $requirement->load(['requestingUnit', 'consultants']);

            } catch (Exception $e) {
                throw new Exception("Error en la persistencia del requerimiento: " . $e->getMessage());
            }
        });


        
    }

    /**
     * Proceso del Momento 2: Registro/Actualización de Estimación
     * Responsabilidad: Consultor CSPE
     */
    public function updateEstimation(string $requirementId, array $estimationData)
    {
        return DB::transaction(function () use ($requirementId, $estimationData) {
            // 1. Buscamos el requerimiento para asegurar que existe
            $requirement = Requirement::findOrFail($requirementId);

            // 2. Creamos o actualizamos la estimación vinculada
            // El primer array busca por estos campos, el segundo actualiza/inserta los datos
            $estimation = $requirement->estimation()->updateOrCreate(
                ['requirement_id' => $requirementId],
                $estimationData
            );

            // 3. Opcional: Podríamos actualizar la fase si el negocio lo requiere
            // $requirement->update(['fase_actual' => 'ES']); 

            return $estimation;
        });
    }
}