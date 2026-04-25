<?php

namespace Tests\Feature\Integrity;

use App\Jobs\VerifyIdentityIntegrity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery;

uses(DatabaseTransactions::class);

describe('US18: Integridad de Datos Asíncrona', function () {

    test('Escenario 01: Verificación automática de referencias UUID', function () {
        Queue::fake();

        // Despachamos el Job
        VerifyIdentityIntegrity::dispatch();

        // Verificamos que el Job se fue a la cola de Redis correctamente
        Queue::assertPushed(VerifyIdentityIntegrity::class);
    });

    test('Escenario 02: Alerta por inconsistencias detectadas', function () {
        // Creamos un registro huérfano en audit_logs (UUID que no existe en users)
        $fakeUuid = \Illuminate\Support\Str::uuid();
        DB::table('audit_logs.activity_logs')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'affected_user_id' => $fakeUuid, // Este UUID no existe
            'action' => 'test_inconsistency',
            'payload' => json_encode([]),
            'created_at' => now()
        ]);

        Log::shouldReceive('critical')
            ->once()
            ->with(Mockery::on(fn($msg) => str_contains($msg, $fakeUuid)));

        // Ejecutamos el Job sincrónicamente para el test
        (new VerifyIdentityIntegrity())->handle();
    });

  test('Escenario 03: Resiliencia del Job ante caída de servicios (Robustez)', function () {
        $job = new VerifyIdentityIntegrity();

        // Verificamos primero que la configuración de reintentos existe
        expect(property_exists($job, 'tries'))->toBeTrue();
        expect($job->tries)->toBe(3);

        // Simulamos la caída de la DB
        DB::shouldReceive('table')
            ->once()
            ->andThrow(new \Illuminate\Database\QueryException(
                'test', 'SELECT...', [], new \Exception('Cnx lost')
            ));

        // Al ejecutar, el Job lanzará la excepción, la cual será capturada por el 
        // Worker de Laravel para gestionar el reintento basado en esos $tries.
        try {
            $job->handle();
        } catch (\Exception $e) {
            expect($e->getMessage())->toContain('Cnx lost');
        }
    });

    test('Escenario 04: Prevención de ejecución duplicada (Atomic Locks)', function () {
    // Se usa el facade Cache para simular el bloqueo que el Job buscará
    // Laravel usa el prefijo 'laravel_cache' por defecto
    \Illuminate\Support\Facades\Cache::lock('verify_integrity_lock', 600)->get();

    $job = new VerifyIdentityIntegrity();
    
    // Al intentar ejecutarlo manualmente, debería devolver false debido al lock activo
    $result = $job->handle();
    
    expect($result)->toBeFalse();
    
    // Limpiamos el lock para no afectar otros tests
    \Illuminate\Support\Facades\Cache::lock('verify_integrity_lock')->release();
});
});