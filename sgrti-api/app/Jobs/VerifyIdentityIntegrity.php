<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyIdentityIntegrity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
{
    Log::info("=== Verificando Integridad Referencial ===");

    try {
        // Verificamos si la tabla de auditoría existe antes de consultar
        // Si no existe, simplemente terminamos con éxito (nada que limpiar)
        $tableExists = DB::select("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE  table_schema = 'audit_logs' 
            AND    table_name   = 'activity_logs'
        )");

        if (!$tableExists[0]->exists) {
            Log::info("Integridad: Esquema de auditoría vacío. Verificación omitida.");
            return;
        }

        $orphans = DB::table('audit_logs.activity_logs as a')
            ->leftJoin('identity.users as u', 'a.user_id', '=', 'u.id')
            ->whereNull('u.id')
            ->count();

        Log::info("Integridad: Verificación completada. Huérfanos: " . $orphans);

    } catch (\Exception $e) {
        Log::error("Error en Job de Integridad: " . $e->getMessage());
    }
}
}