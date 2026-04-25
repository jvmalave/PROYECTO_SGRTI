<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Creamos el usuario administrador para el Sprint 1
        User::create([
            'name'     => 'Admin SGRTI',
            'email'    => 'admin@sgrti.com',
            'password' => Hash::make('secret123'),
            'roles'    => json_encode(['admin']), // US03: Base para RBAC
        ]);
    }
}