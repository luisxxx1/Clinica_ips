<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_exams', function (Blueprint $table) {
            // Agregamos la columna como nullable por si no siempre hay observaciones
            $table->text('observations')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('medical_exams', function (Blueprint $table) {
            $table->dropColumn('observations');
        });
    }
};
