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