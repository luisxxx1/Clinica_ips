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
            // Agregamos campos de personalización después del email
            $table->string('job_title')
                  ->nullable()
                  ->after('email')
                  ->comment('Cargo específico del profesional (ej. Especialista en Optometría)');

            $table->string('ui_color', 20)
                  ->default('#3b82f6') // Azul por defecto (Tailwind blue-500)
                  ->after('job_title')
                  ->comment('Color hexadecimal para la identidad visual del usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminamos las columnas si se revierte la migración
            $table->dropColumn(['job_title', 'ui_color']);
        });
    }
};