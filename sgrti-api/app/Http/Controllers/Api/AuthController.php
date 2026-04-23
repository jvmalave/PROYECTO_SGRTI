<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard; 
use Illuminate\Contracts\Auth\StatefulGuard;


class AuthController extends Controller
{
    /**
     * Obtenemos el guard y le indicamos al editor qué métodos esperar
     * @return \Tymon\JWTAuth\JWTAuth|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard(): Guard|StatefulGuard
    {
        return auth('api');
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // attempt() ahora será reconocido por el editor
        if (!$token = $this->guard()->attempt($credentials)) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => $this->guard()->user()
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json($this->guard()->user());
    }

    public function logout(): JsonResponse
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
}