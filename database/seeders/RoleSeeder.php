<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Registra los roles oficiales con sus colores de identidad visual.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador',     'color' => 'slate'],  // Control total
            ['name' => 'Admisión',          'color' => 'emerald'], // Recepción y matrículas
            ['name' => 'Medicina General',  'color' => 'blue'],
            ['name' => 'Optometría',        'color' => 'cyan'],
            ['name' => 'Odontología',       'color' => 'indigo'],
            ['name' => 'Psicología',        'color' => 'purple'],
            ['name' => 'Fonoaudiología',    'color' => 'orange'],
            ['name' => 'Audiometría',       'color' => 'amber'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']], // Busca por nombre
                ['color' => $role['color']] // Si no existe, lo crea con este color
            );
        }
    }
}