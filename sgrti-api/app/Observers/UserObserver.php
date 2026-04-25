<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;




class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
  
    public function updated(User $user): void
{
    // Solo registramos si el campo 'roles' fue modificado
    if ($user->wasChanged('roles')) {
        DB::table('audit_logs.activity_logs')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'causer_id' => auth('api')->id(),
            'affected_user_id' => $user->id,
            'action' => 'roles_updated',
            'payload' => json_encode([
                'old' => $user->getOriginal('roles'),
                'new' => $user->roles
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
