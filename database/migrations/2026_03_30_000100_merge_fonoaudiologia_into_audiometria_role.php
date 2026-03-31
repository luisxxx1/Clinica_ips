<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fusiona Fonoaudiología en Audiometría.
     */
    public function up(): void
    {
        $now = now();

        $audiometriaRole = DB::table('roles')
            ->whereIn('name', ['Audiometría', 'Audiometria'])
            ->first();

        if (!$audiometriaRole) {
            $audiometriaId = DB::table('roles')->insertGetId([
                'name' => 'Audiometría',
                'color' => 'amber',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $audiometriaId = $audiometriaRole->id;
        }

        $fonoRoleIds = DB::table('roles')
            ->whereIn('name', ['Fonoaudiología', 'Fonoaudiologia'])
            ->pluck('id');

        if ($fonoRoleIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('role_id', $fonoRoleIds->all())
                ->update(['role_id' => $audiometriaId]);

            DB::table('roles')
                ->whereIn('id', $fonoRoleIds->all())
                ->delete();
        }

        // No se alteran requested_areas, exam_results ni clinical_histories.
        // La fusión aplica solo a cuentas/roles de usuario.
    }

    /**
    * Restaura el rol eliminado, sin cambiar asignaciones de usuarios.
     */
    public function down(): void
    {
        $exists = DB::table('roles')
            ->where('name', 'Fonoaudiología')
            ->exists();

        if (!$exists) {
            DB::table('roles')->insert([
                'name' => 'Fonoaudiología',
                'color' => 'orange',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
