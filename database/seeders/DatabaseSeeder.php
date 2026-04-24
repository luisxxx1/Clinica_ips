<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ejecutamos la creación de roles (Asegúrate de que RoleSeeder tenga los colores)
        $this->call(RoleSeeder::class);

        // 2. Buscamos el ID del rol Administrador
        $adminRole = Role::where('name', 'Administrador')->first();

        // 3. Usamos updateOrCreate para que puedas correr el seeder varias veces sin errores
        User::updateOrCreate(
            ['email' => 'admin@clinicaips.com'], // Único por email
            [
                'name' => 'Juan Pablo - SnakeDev',
                'password' => Hash::make('admin1234'),
                'role_id' => $adminRole->id,
                'job_title' => 'Director General / Desarrollador',
                'ui_color' => '#1e293b', // Color Slate-800 para el admin
                'email_verified_at' => now(),
            ]
        );

        // 4. (Opcional) Crear un usuario de Admisión para pruebas rápidas
        $admisionRole = Role::where('name', 'Admisión')->first();
        if ($admisionRole) {
            User::updateOrCreate(
                ['email' => 'recepcion@clinicaips.com'],
                [
                    'name' => 'Ana Recepción',
                    'password' => Hash::make('recepcion123'),
                    'role_id' => $admisionRole->id,
                    'job_title' => 'Coordinadora de Admisiones',
                    'ui_color' => '#10b981', // Emerald-500
                ]
            );
        }
    }
}
