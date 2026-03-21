<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Se mantiene el nullable() para no romper registros existentes.
            // Una vez ejecutada, deberías asignar roles y luego podrías 
            // hacer otra migración para quitar el nullable si lo deseas.
            $table->foreignId('role_id')
                  ->after('email')
                  ->nullable() 
                  ->constrained('roles')
                  ->onDelete('set null'); // Cambiado a 'set null' para no borrar al usuario si el rol desaparece
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Es buena práctica usar el nombre completo de la relación si da problemas
            $table->dropForeign(['role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            // Borramos la columna en un callback separado para asegurar compatibilidad
            $table->dropColumn('role_id');
        });
    }
};