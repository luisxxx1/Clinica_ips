<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('area', 100);
            $table->string('title', 150)->nullable();
            $table->text('entry');
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'recorded_at']);
            $table->index('area');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_histories');
    }
};
