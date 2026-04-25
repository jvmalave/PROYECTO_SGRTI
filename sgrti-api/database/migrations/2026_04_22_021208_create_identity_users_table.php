<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Obtener el nombre del esquema desde el .env
        $schema = env('SCHEMA_IDENTITY', 'identity');

        // 2. Crea el esquema si no existe (esto es importante para evitar errores si el esquema no ha sido creado manualmente)
        DB::statement("CREATE SCHEMA IF NOT EXISTS $schema");

        // 3. Selecciona este esquema para esta migración
        DB::statement("SET search_path TO $schema");

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // US02: Llave primaria UUID
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->jsonb('roles')->nullable(); // Para el RBAC futuro
            $table->rememberToken();
            $table->timestamps();
        });

        // 3. Volver al esquema público para no afectar otras migraciones
        DB::statement("SET search_path TO public");
    }

    public function down(): void
    {
        $schema = env('SCHEMA_IDENTITY', 'identity');
        Schema::dropIfExists("$schema.users");
    }
};


