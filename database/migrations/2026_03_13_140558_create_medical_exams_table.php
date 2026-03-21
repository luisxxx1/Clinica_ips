<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_exams', function (Blueprint $table) {
            $table->id();

            // Relación con el estudiante
            $table->foreignId('student_id')->constrained('students')->onDelete('restrict');

            // Quién creó la orden original (Admisiones)
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // Áreas que el niño debe visitar: ["psicologia", "medicina_general"]
            $table->json('requested_areas'); 

            // Estado global: pendiente, en_proceso, completado
            $table->string('status')->default('pendiente')->index();

            // Conclusión final del circuito
            $table->string('result_type')->nullable(); // Ej: Apto, No Apto
            $table->text('final_observations')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Importante en registros médicos

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_exams');
    }
};