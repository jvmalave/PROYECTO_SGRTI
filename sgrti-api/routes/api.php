<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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