<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Core\RequirementController;

// --- Rutas Públicas ---

Route::post('login', [AuthController::class, 'login']);

// --- Rutas Protegidas (JWT) ---
Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:api'])->group(function () {
    
    // Ruta solo para administradores
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Bienvenido, Admin']);
    })->middleware('role:admin');

    // Ruta solo para auditores
    Route::get('/auditor/reports', function () {
        return response()->json(['message' => 'Reportes de Auditoría']);
    })->middleware('role:auditor');
    
});

/**
 * --- Rutas del Core (Sprint 2) ---
 * Por ahora las dejamos fuera del middleware 'auth:api' para que puedas
 * probar en Thunder Client sin lidiar con el Token JWT.
 */
Route::prefix('v1/core')->group(function () {
    
    // Momento 1: Registro de Requerimiento (CU-004 + CU-012)
    Route::post('requirements', [RequirementController::class, 'store']);

    // Edición de Requerimiento (CU-007)
    Route::put('requirements/{id}', [RequirementController::class, 'update']);

    // Momento 2 :Estimación
    // El {id} será el UUID del requerimiento que recibiste en el Momento 1
    Route::put('requirements/{id}/estimation', [RequirementController::class, 'updateEstimation']);

  // Listado de requerimientos con filtros (US-005)

     Route::get('requirements', [RequirementController::class, 'index']); // Listar

    // Detalle de requerimiento (US-006)

     Route::get('requirements/{id}', [RequirementController::class, 'show']);  // Detalle

    // Delete (eliminación lógica, no física)
     Route::delete('requirements/{id}', [RequirementController::class, 'destroy']); // Eliminación lógica
});







