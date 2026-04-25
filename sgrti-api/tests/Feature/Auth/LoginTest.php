<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\{Redis, Hash};
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);


describe('US01: Autenticación y Gestión de Sesiones', function () {

    test('Escenario 01: Inicio de sesión exitoso y generación de Token', function () {
        $user = User::factory()->create(['password' => Hash::make('secret123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['access_token']);
        expect(explode('.', $response->json('access_token')))->toHaveCount(3);
    });

    test('Escenario 02: Persistencia de estado en Redis', function () {
        $user = User::factory()->create();
        // Usamos el guard explícito de la API
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $keys = Redis::keys('*jwt*');
        expect($keys)->not->toBeEmpty();
    });

    test('Escenario 03: Bloqueo de cuenta tras 3 intentos fallidos', function () {
        $user = User::factory()->create();
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong']);
        }
        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'secret123']);
        $response->assertStatus(423); // Locked
    });

    test('Escenario 04: Intento de acceso con Token expirado (Robustez)', function () {
        $user = User::factory()->create();
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        
        $this->travel(2)->hours();

        $response = $this->withToken($token)->getJson('/api/me');
        $response->assertStatus(401);
    });

    test('Escenario 05: Intento de acceso con Token manipulado (Robustez)', function () {
        $token = "fake.payload.signature";
        $response = $this->withToken($token)->getJson('/api/me');
        $response->assertStatus(401);
    });
});
