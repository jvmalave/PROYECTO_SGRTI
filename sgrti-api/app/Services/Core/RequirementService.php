<?php

namespace App\Services\Core;

use App\Models\Core\Requirement;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\PhaseTransitionNotification;

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
            $requirement = Requirement::with(['requestingUnit', 'consultants'])->findOrFail($requirementId);

            // 1. Guardar/Actualizar estimación
            $estimation = $requirement->estimation()->updateOrCreate(
                ['requirement_id' => $requirementId],
                $estimationData
            );

            // 2. Definir campos para verificar integridad
            $requiredFields = [
                'h_analisis', 'f_analisis_ini', 'f_analisis_fin',
                'h_diseno', 'f_diseno_ini', 'f_diseno_fin',
                'h_construccion', 'f_construccion_ini', 'f_construccion_fin',
                'h_pruebas', 'f_prueba_ini', 'f_prueba_fin',
                'h_certificacion', 'f_certificacion_ini', 'f_certificacion_fin',
                'h_implementacion', 'f_implementacion_ini', 'f_implementacion_fin'
            ];

            // 3. Validar si está completo y si las horas totales > 0
            $isComplete = true;
            $totalHours = 0;

            foreach ($requiredFields as $field) {
                if (empty($estimation->$field)) {
                    $isComplete = false;
                }
                // Sumamos si el campo es de horas
                if (str_starts_with($field, 'h_')) {
                    $totalHours += (float) ($estimation->$field ?? 0);
                }
            }

            // 4. Hard Gate: Salto a ATF
            if ($isComplete && $totalHours > 0) {
                $requirement->update(['fase_actual' => 'ATF']);
                
                // 5. Notificación (Llamada al método de envío)
                $this->sendPhaseCompletionEmail($requirement);
            }

            return $estimation;
        });
    }

    /**
     * Obtener listado de requerimientos con filtros (US05)
     */
    public function getRequirementsList(array $filters = [])
    {
        // Usamos 'with' para traer las relaciones de una vez (Eager Loading)
        $query = Requirement::with(['requestingUnit', 'consultants']);

        // Filtro por número de RRTI (Búsqueda parcial: ej. "001")
        if (!empty($filters['numero_rrti'])) {
            $query->where('numero_rrti', 'like', '%' . $filters['numero_rrti'] . '%');
        }

        // Filtro por Fase (PL, ES, etc.)
        if (!empty($filters['fase'])) {
            $query->where('fase_actual', $filters['fase']);
        }

        // Filtro por Año
        if (!empty($filters['anio'])) {
            $query->where('anio', $filters['anio']);
        }

        // Ordenamos por los más recientes primero y paginamos (10 por página)
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }


    /**
     * Lógica para envío de notificaciones
     */
    protected function sendPhaseCompletionEmail($requirement)
    {
        // Recopilar correos:
        $emails = [];
        
        // Correo del contacto funcional (Unidad Solicitante)
        if ($requirement->requestingUnit->contacto_funcional_correo) {
            $emails[] = $requirement->requestingUnit->contacto_funcional_correo;
        }

        // Correos de los consultores CSPE asignados y coordinador
        foreach ($requirement->consultants as $consultant) {
            // Aquí asumo que tienes una relación con el modelo User o el campo correo
            // Por ahora lo dejamos listo para implementar el Mailable de Laravel
            // $emails[] = $consultant->email; 
        }

        // El envío real se hace con:
        // Mail::to($emails)->send(new PhaseTransitionNotification($requirement));
        if (!empty($emails)) {
        Mail::to($emails)->send(new PhaseTransitionNotification($requirement));
    }
        
        // Para tu prueba en Thunder Client, puedes poner un Log para verificar:
        // \Log::error("Email de notificación enviado para el requerimiento: " . $requirement->numero_rrti);
    }


}