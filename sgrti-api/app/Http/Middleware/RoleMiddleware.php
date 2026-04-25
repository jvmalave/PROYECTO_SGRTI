<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string $role El rol requerido para acceder a la ruta
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        // 1. Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // 2. Verificar si el rol solicitado existe en el array JSONB 'roles'
        $userRoles = $user->roles ?? [];

        if (!in_array($role, $userRoles)) {
            return response()->json([
                'message' => 'Acceso denegado: permisos insuficientes'
            ], 403); // 403 Forbidden es más apropiado para denegar acceso por roles 
        }
        return $next($request);
    }
}