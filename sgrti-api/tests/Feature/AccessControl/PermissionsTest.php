<?php

namespace Tests\Feature\AccessControl;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

describe('US03: Control de Acceso y Auditoría', function () {

    test('Escenario 01: Protección de rutas mediante Middleware RBAC', function () {
        // Creamos un usuario con rol 'operator'
        $user = User::factory()->create(['roles' => ['operator']]);
        $token = auth('api')->fromUser($user);

        // Simulamos una petición a una ruta que requiere 'admin'
        $response = $this->withToken($token)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Acceso denegado: permisos insuficientes']);
    });

    test('Escenario 02: Registro de auditoría por cambio de privilegios', function () {
        $admin = User::factory()->create(['roles' => ['admin']]);
        $targetUser = User::factory()->create(['roles' => ['operator']]);

        // Actuamos como admin y cambiamos el rol al usuario afectado
        auth('api')->login($admin);
        
        $oldRoles = $targetUser->roles;
        $newRoles = ['admin', 'operator'];
        
        $targetUser->update(['roles' => $newRoles]);

        // Verificamos que exista el registro en el esquema audit_logs
        $log = DB::table('audit_logs.activity_logs')
            ->where('affected_user_id', $targetUser->id)
            ->first();

        expect($log)->not->toBeNull();
        expect($log->causer_id)->toBe($admin->id);
        
        $payload = json_decode($log->payload);
        expect($payload->old)->toBe($oldRoles);
        expect($payload->new)->toBe($newRoles);
    });

    test('Escenario 03: Validación de múltiples roles (Robustez)', function () {
        // Usuario con múltiples roles en JSONB
        $user = User::factory()->create(['roles' => ['operator', 'auditor']]);
        $token = auth('api')->fromUser($user);

        // Intentamos entrar a una ruta que requiere 'auditor'
        // El middleware debe ser capaz de buscar dentro del array
        $response = $this->withToken($token)
            ->getJson('/api/auditor/reports');

        $response->assertStatus(200);
    });
});