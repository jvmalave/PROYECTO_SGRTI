<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Aseguramos el esquema lógico para el dominio Core
        DB::statement('CREATE SCHEMA IF NOT EXISTS requirements_core');

        // 2. Tabla de Requerimientos (El Padre - Momento 1)
        Schema::create('requirements_core.requirements', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Identificador único universal
            $table->string('numero_rrti')->unique(); // Ej: 40721
            $table->string('tipo_requerimiento'); // Ej: Masivo, Específico, etc.
            $table->integer('anio');
            $table->date('fecha_creacion');
            $table->string('doc_solicitud_ti')->nullable(); // Ruta del PDF
            $table->string('planilla_necesidades')->nullable(); // Ruta del PDF
            $table->text('descripcion_detallada');
            
            // Ciclo de vida WATCH
            $table->enum('fase_actual', ['PL', 'ATF', 'GR', 'FC'])->default('PL');
            $table->string('estado_interno')->default('Abierto'); // Ej: Abierto, En Proceso, Cerrado, etc.
            
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // Borrado lógico para auditoría
        });

        // 3. Tabla de Unidad Solicitante (Momento 1 - CU-012)
        Schema::create('requirements_core.requesting_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('requirement_id'); // Relación con el padre
            $table->string('sociedad'); // Ej: TELCO, MOVILNET, etc.
            $table->string('sistema'); // Ej: SAP, CRM, etc.
            $table->string('unidad_solicitante'); 
            $table->string('contacto_funcional_nom'); 
            $table->string('contacto_funcional_telf');
            $table->string('contacto_funcional_correo');
            $table->string('contacto_gpgti_nom');
            $table->string('contacto_gpgti_correo');

            $table->foreign('requirement_id')->references('id')->on('requirements_core.requirements')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Tabla de Consultores Asignados (Momento 1 - Relación)
        Schema::create('requirements_core.requirement_consultants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('requirement_id');
            $table->uuid('user_uuid'); // ID del consultor en el esquema identity
            $table->string('rol_cspe')->default('Responsable');

            $table->foreign('requirement_id')->references('id')->on('requirements_core.requirements')->onDelete('cascade');
            $table->timestamps();
        });

        // 5. Tabla de Estimación (Momento 2 - CU-011 - Todo es Nullable inicialmente)
        Schema::create('requirements_core.estimations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('requirement_id');
            
            // Horas por subfase
            $table->integer('h_analisis')->default(0);
            $table->integer('h_diseno')->default(0);
            $table->integer('h_construccion')->default(0);
            $table->integer('h_pruebas')->default(0);
            $table->integer('h_certificacion')->default(0);
            $table->integer('h_implementacion')->default(0);

            // Fechas por subfase (Hitos)
            $table->date('f_analisis_ini')->nullable(); $table->date('f_analisis_fin')->nullable();
            $table->date('f_diseno_ini')->nullable(); $table->date('f_diseno_fin')->nullable();
            $table->date('f_construccion_ini')->nullable(); $table->date('f_construccion_fin')->nullable();
            $table->date('f_prueba_ini')->nullable(); $table->date('f_prueba_fin')->nullable();
            $table->date('f_certificacion_ini')->nullable(); $table->date('f_certificacion_fin')->nullable();
            $table->date('f_implementacion_ini')->nullable(); $table->date('f_implementacion_fin')->nullable();

            $table->foreign('requirement_id')->references('id')->on('requirements_core.requirements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirements_core.estimations');
        Schema::dropIfExists('requirements_core.requirement_consultants');
        Schema::dropIfExists('requirements_core.requesting_units');
        Schema::dropIfExists('requirements_core.requirements');
    }
};