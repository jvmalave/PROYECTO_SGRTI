<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la optimización de la capa de persistencia del esquema Core.
 * Objetivo: Cumplir con la US05 (Dashboard de Gestión) y mejorar la latencia de consultas[cite: 9].
 */
return new class extends Migration
{
    /**
     * Ejecuta las optimizaciones de infraestructura de datos.
     */
    public function up(): void
    {
        Schema::table('requirements_core.requirements', function (Blueprint $table) {
            /**
             * Índice para búsqueda exacta por identificador RRTI (SSOT).
             * Optimiza el CU-003: Buscar Requerimiento dentro del ecosistema[cite: 1, 9].
             */
            $table->index('numero_rrti', 'idx_requirements_rrti_btree');

            /**
             * Índice para el filtrado por fases de la Máquina de Estados.
             * Crucial para el rendimiento del Dashboard y el semáforo visual[cite: 1, 11].
             */
            $table->index('fase_actual', 'idx_requirements_phase_btree');

            /**
             * Índice Compuesto: Optimiza consultas que filtran por año y fase simultáneamente.
             * Diseñado para la generación de reportes mensuales de la GSGE[cite: 3, 11].
             */
            $table->index(['anio', 'fase_actual'], 'idx_requirements_year_phase_composite');
        });
    }

    /**
     * Revierte los índices de rendimiento aplicados.
     */
    public function down(): void
    {
        Schema::table('requirements_core.requirements', function (Blueprint $table) {
            $table->dropIndex('idx_requirements_rrti_btree');
            $table->dropIndex('idx_requirements_phase_btree');
            $table->dropIndex('idx_requirements_year_phase_composite');
        });
    }
};