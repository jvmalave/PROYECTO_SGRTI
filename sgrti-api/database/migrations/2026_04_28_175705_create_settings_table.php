<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('requirements_core.settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique(); // Ej: 'special_ops_key'
        $table->text('value');
        $table->string('description')->nullable();
        $table->timestamps();
    });

    // Insertar la clave inicial por defecto
    DB::table('requirements_core.settings')->insert([
        'key' => 'special_ops_key',
        'value' => 'CANTV_2026_SECURE', // Clave inicial
        'description' => 'Clave para operaciones especiales de borrado lógico',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements_core.settings');
    }
};
