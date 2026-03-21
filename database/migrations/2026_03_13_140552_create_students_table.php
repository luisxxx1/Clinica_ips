<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // --- DATOS DEL ESTUDIANTE ---
            $table->string('document_type', 10); // RC, TI, CC, CE
            $table->string('document_number', 30)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->integer('age');
            $table->string('gender', 20);
            $table->string('previous_school')->nullable(); // Nullable por si es primer ingreso
            $table->string('grade', 50);

            // --- DATOS DEL ACUDIENTE ---
            $table->string('guardian_name', 100);
            $table->string('guardian_lastname', 100);
            $table->string('guardian_document', 30);
            $table->integer('guardian_age');
            $table->string('guardian_phone', 30);
            $table->string('guardian_address');
            $table->string('guardian_relationship', 50);
            $table->string('guardian_email', 100);

            $table->timestamps();
            $table->softDeletes(); // Permite recuperar registros borrados

            // Índices para velocidad de búsqueda
            $table->index('document_number');
            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};