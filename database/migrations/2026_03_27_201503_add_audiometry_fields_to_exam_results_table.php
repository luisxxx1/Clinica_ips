<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            // Guardamos la ruta de la imagen generada por el Canvas
            $table->string('chart_path')->nullable()->after('notes');
            
            // Campos para el Promedio de Tonos Audibles (Cálculo clínico)
            $table->decimal('pta_od', 5, 2)->nullable()->after('chart_path');
            $table->decimal('pta_oi', 5, 2)->nullable()->after('pta_od');
        });
    }

    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropColumn(['chart_path', 'pta_od', 'pta_oi']);
        });
    }
};