<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Importante para SET search_path

return new class extends Migration
{
    public function up(): void
{
   // 1. Obtener el nombre del esquema desde el .env
    $schema = env('SCHEMA_IDENTITY', 'identity');

    // 2. Crea el esquema si no existe (esto es importante para evitar errores si el esquema no ha sido creado manualmente)
    DB::statement("CREATE SCHEMA IF NOT EXISTS $schema");

    // 3. Selecciona el esquema para esta migración
    DB::statement("SET search_path TO $schema");

    Schema::create('personal_access_tokens', function (Blueprint $table) {
        $table->id();
        $table->string('tokenable_type');
        $table->uuid('tokenable_id'); 
        $table->index(['tokenable_type', 'tokenable_id']);
        $table->string('name');
        $table->string('token', 64)->unique();
        $table->text('abilities')->nullable();
        $table->timestamp('last_used_at')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->timestamps();
    });

    // 3.  Volver al esquema público para no afectar otras migraciones
    DB::statement("SET search_path TO public");
}

    public function down(): void
    {
        $schema = env('SCHEMA_IDENTITY', 'identity');
        Schema::dropIfExists("$schema.personal_access_tokens");
    }
};



