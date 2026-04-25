<?php

namespace Tests\Feature\Database;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

describe('US02: Arquitectura Multiesquema y Persistencia', function () {

    // test('Escenario 01: Verificación de estructura multiesquema', function () {
    //     $schemas = collect(DB::select("SELECT schema_name FROM information_schema.schemata"))
    //         ->pluck('schema_name')
    //         ->toArray();

    //     // $expectedSchemas = ['identity', 'requirements_core', 'execution_flow', 'reporting_kpi', 'audit_logs'];
    //     $expectedSchemas = ['identity', 'audit_logs'];

    //     foreach ($expectedSchemas as $schema) {
    //         expect($schemas)->toContain($schema);
    //     }

    //     $publicTables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    //     // Incluimos las tablas que Laravel crea por defecto en el esquema público
    //     $allowedInPublic = ['migrations', 'cache', 'cache_locks', 'personal_access_tokens', 'failed_jobs', 'jobs'];

    //     foreach ($publicTables as $table) {
    //         expect($allowedInPublic)->toContain($table->table_name);
    //     }
    // });

    test('Escenario 01: Verificación de estructura multiesquema', function () {
        // Obtenemos los esquemas reales de la base de datos
        $schemasInDb = collect(DB::select("SELECT schema_name FROM information_schema.schemata"))
            ->pluck('schema_name')
            ->toArray();

        // Esquemas que OBLIGATORIAMENTE deben estar en el Sprint 1
        $requiredSchemas = ['identity', 'audit_logs'];

        foreach ($requiredSchemas as $schema) {
            expect($schemasInDb)->toContain($schema);
        }
    });

    test('Escenario 02: Uso de UUID como identificador primario', function () {
        $user = User::factory()->create();
        expect($user->id)->toBeString();
        expect($user->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
    });

    test('Escenario 03: Aislamiento por dominio (Permisos de DB)', function () {
        try {
            // Intentar una operación de catálogo que requiere privilegios de sistema nivel 1
            // Esto fallará para sgrti_admin porque no es un "true superuser"
            DB::statement("CREATE TABLE pg_catalog.test_table (id int)");
            $this->fail("El sistema debería haber denegado la creación en el catálogo de sistema.");
        } catch (\Exception $e) {
            $errorMessage = strtolower($e->getMessage());
            // Validamos que el error sea de permisos o de sistema
            expect($errorMessage)->toMatch('/permission denied|must be superuser|read-only/');
        }
    });

    test('Escenario 04: Prevención de colisiones de UUID (Robustez)', function () {
        $count = 100;
        $users = User::factory()->count($count)->create();
        $ids = $users->pluck('id');
        expect($ids->unique()->count())->toBe($count);
    });


    test('Escenario 05: Prevención de contaminación del esquema público', function () {
        $publicTables = collect(DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'"))
            ->pluck('table_name')
            ->toArray();

        $allowedInPublic = ['migrations', 'cache', 'cache_locks', 'personal_access_tokens', 'failed_jobs', 'jobs'];

        foreach ($publicTables as $table) {
            expect($allowedInPublic)->toContain($table);
        }
    });
});