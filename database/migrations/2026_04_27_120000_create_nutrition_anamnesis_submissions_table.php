<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_anamnesis_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->json('payload');
            $table->string('submitted_by', 40)->default('student_link');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['professional_id', 'student_id'], 'idx_nutri_anamnese_prof_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_anamnesis_submissions');
    }
};

