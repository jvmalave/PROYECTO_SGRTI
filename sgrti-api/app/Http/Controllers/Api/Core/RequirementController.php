<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\StoreRequirementRequest; // Validación específica para el registro inicial
use App\Http\Requests\Core\UpdateEstimationRequest; // Validación específica para la estimación
use App\Services\Core\RequirementService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

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


    #[OA\Post(
        path: "/api/v1/core/requirements",
        summary: "Crear un nuevo requerimiento",
        tags: ["Requirements"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["title", "description", "priority"],
            properties: [
                new OA\Property(property: "title", type: "string", example: "Falla en nodo central"),
                new OA\Property(property: "description", type: "string", example: "Se requiere revisión de fibra"),
                new OA\Property(property: "priority", type: "string", example: "ALTA")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Requerimiento creado exitosamente"
    )]
    #[OA\Response(response: 422, description: "Error de validación")]
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

    #[OA\Put(
        path: "/api/v1/core/requirements/{id}",
        summary: "Actualizar requerimiento (Sujeto a bloqueo ATF)",
        tags: ["Requirements"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID del requerimiento",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Requerimiento actualizado"
    )]
    #[OA\Response(
        response: 403,
        description: "Acceso denegado: Requerimiento bloqueado por fase ATF"
    )]

    // CU-007: Edición de Requerimiento (Solo campos permitidos, sin afectar la fase ni la asignación de consultores)
    public function update(StoreRequirementRequest $request, string $id): JsonResponse
    {
        try {
            // validated() solo devolverá los campos que pasaron el filtro
            $updated = $this->requirementService->updateRequirement($id, $request->validated());
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Requerimiento actualizado correctamente',
                'data' => $updated
            ], 200);
        } catch (Exception $e) {
            return response()->json([
              'status' => 'error', 
              'message' => $e->getMessage()], 
              422);
        }
    }


    /**
 * Momento 2: Registro de Estimación (Responsabilidad: Consultor CSPE)
 * CU-011: Estimar Requerimiento
 */

    #[OA\Patch(
        path: "/api/v1/core/requirements/{id}/estimation",
        summary: "Actualizar datos de estimación técnica",
        tags: ["Requirements"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "horas_estimadas", type: "integer", example: 40),
                new OA\Property(property: "costo_estimado", type: "number", format: "float", example: 1500.50),
                new OA\Property(property: "tecnico_asignado", type: "string", example: "Ing. Juan Pérez")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Estimación actualizada correctamente")]
    #[OA\Response(response: 422, description: "Datos de estimación inválidos")]
    #[OA\Response(response: 500, description: "Error interno del servidor")]

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

    #[OA\Get(
        path: "/api/v1/core/requirements",
        summary: "Obtener lista de requerimientos",
        tags: ["Requirements"]
    )]
    #[OA\Response(
        response: 200,
        description: "Lista recuperada exitosamente"
    )]
    public function index(Request $request): JsonResponse
    {
        try {
            // El método index por estándar REST se usa para listados
            $requirements = $this->requirementService->getRequirementsList($request->all());

            return response()->json([
                'status' => 'success',
                'data'   => $requirements
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener el listado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar el detalle de un requerimiento específico.
     */

    #[OA\Get(
        path: "/api/v1/core/requirements/{id}",
        summary: "Consultar detalle de un requerimiento",
        tags: ["Requirements"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "UUID o ID del requerimiento",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Detalle del requerimiento encontrado"
    )]
    #[OA\Response(response: 404, description: "Requerimiento no encontrado")]

    public function show(string $id): JsonResponse
    {
        try {
            $requirement = $this->requirementService->getRequirementById($id);

            return response()->json([
                'status' => 'success',
                'data'   => $requirement
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Requerimiento no encontrado o error: ' . $e->getMessage()
            ], 404); // Retornamos 404 si el UUID no existe
        }
    }

    #[OA\Delete(
        path: "/api/v1/core/requirements/{id}",
        summary: "Eliminar requerimiento con clave especial",
        tags: ["Requirements"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Parameter(
        name: "special_key",
        in: "query",
        required: true,
        description: "Clave de Operaciones Especiales",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(response: 204, description: "Eliminado con éxito")]
    #[OA\Response(response: 401, description: "Clave de seguridad incorrecta")]

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            // Validamos que envíen la clave en el body
            $request->validate([
                'special_key' => 'required|string'
            ]);

            $this->requirementService->deleteRequirement($id, $request->special_key);

            return response()->json([
                'status' => 'success',
                'message' => 'Requerimiento eliminado lógicamente con autorización.'
            ], 200);

        } catch (Exception $e) {
            // Si la excepción es por la clave, podemos devolver un 403 (Prohibido)
            $code = $e->getMessage() == "Clave de operaciones especiales incorrecta. Acción denegada." ? 403 : 500;
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $code);
        }
    }
}