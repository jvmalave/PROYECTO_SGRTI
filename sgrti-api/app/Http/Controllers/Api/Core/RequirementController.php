<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\StoreRequirementRequest; // Validación específica para el registro inicial
use App\Services\Core\RequirementService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Core\UpdateEstimationRequest; // Validación específica para la estimación
use Exception;

class RequirementController extends Controller
{
    /**
     * @var RequirementService
     */
    protected $requirementService;

    /**
     * Inyección del Servicio de Requerimientos.
     * Laravel resuelve automáticamente la instancia de RequirementService.
     * * @param RequirementService $requirementService
     */
    public function __construct(RequirementService $requirementService)
    {
        $this->requirementService = $requirementService;
    }

    /**
     * Momento 1: Registro Inicial de Requerimiento (Responsabilidad: Coordinador)
     * CU-004: Crear Requerimiento
     * CU-012: Gestionar Unidad Solicitante
     * * El parámetro StoreRequirementRequest valida automáticamente los datos
     * antes de entrar a este método.
     * * @param StoreRequirementRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequirementRequest $request): JsonResponse
    {
      //return response()->json(['debug' => 'El controlador si recibe la llamada']);


        try {
            // El método $request->validated() retorna solo los datos que pasaron las reglas.
            $requirement = $this->requirementService->createInitialRequirement($request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Requerimiento registrado y asignado exitosamente en fase PL.',
                'data'    => $requirement
            ], 201);

        } catch (Exception $e) {
            // Capturamos cualquier error de base de datos o lógica de negocio
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo completar el registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Momento 2: Registro de Estimación (Responsabilidad: Consultor CSPE)
 * CU-011: Estimar Requerimiento
 */
public function updateEstimation(UpdateEstimationRequest $request, string $id): JsonResponse
{
    try {
        $estimation = $this->requirementService->updateEstimation($id, $request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Estimación actualizada correctamente.',
            'data'    => $estimation
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Error al procesar la estimación: ' . $e->getMessage()
        ], 500);
    }
}
}