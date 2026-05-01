<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la reubicación de la auditoría forense de colas.
 * Traslada el registro de fallos al esquema lógico de negocio.
 */
return new class extends Migration
{
    /**
     * Ejecuta la transición de infraestructura de datos.
     */
    public function up(): void
    {
        // 1. Limpieza del esquema public para mantener el orden arquitectónico.
        Schema::dropIfExists('public.failed_jobs');

        // 2. Creación de la tabla en el esquema core con soporte para UUIDs[cite: 1, 11].
        Schema::create('requirements_core.failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // Identificador único para trazabilidad forense.
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload'); // Contiene el requerimiento serializado[cite: 1].
            $table->longText('exception'); // Registro detallado del error de envío[cite: 1].
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Revierte los cambios de infraestructura.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements_core.failed_jobs');
    }
};
