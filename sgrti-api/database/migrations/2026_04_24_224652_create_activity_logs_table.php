<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Aseguramos que el esquema exista (por si acaso)
        DB::statement('CREATE SCHEMA IF NOT EXISTS audit_logs');

        Schema::create('audit_logs.activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('causer_id')->nullable(); // Quién hizo el cambio
            $table->uuid('affected_user_id');      // A quién se le cambió
            $table->string('action');              // 'roles_updated'
            $table->jsonb('payload');              // Datos old/new
            $table->timestamps();

            // Opcional: Índices para búsquedas rápidas
            $table->index('affected_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs.activity_logs');
    }
};