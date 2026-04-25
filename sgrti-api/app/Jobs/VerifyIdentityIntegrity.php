<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyIdentityIntegrity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

/**
     * El número de veces que se reintentará el trabajo si falla.
     */
    public int $tries = 3; // Intentamos 3 veces antes de marcar el Job como fallido

public function handle(): bool
{
    // Escenario 04: Atomic Lock
    $lock = Cache::lock('verify_integrity_lock', 600);

    if (!$lock->get()) {
        // Retornamos false si el lock ya existe (evita duplicados)
        return false; 
    }

    try {
        $orphans = DB::table('audit_logs.activity_logs as a')
            ->leftJoin('identity.users as u', 'a.affected_user_id', '=', 'u.id')
            ->whereNull('u.id')
            ->select('a.affected_user_id')
            ->get();

        foreach ($orphans as $orphan) {
            Log::critical("INCONSISTENCIA DETECTADA: El UUID {$orphan->affected_user_id} no existe");
        }
        
        return true; // Ejecución exitosa
    } finally {
        $lock->release();
    }
}
}