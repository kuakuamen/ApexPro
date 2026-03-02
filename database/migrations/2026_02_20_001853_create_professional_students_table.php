<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('professional_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['personal', 'nutri']);
            $table->timestamps();

            // Evita duplicidade (ex: mesmo aluno com mesmo personal duas vezes)
            $table->unique(['student_id', 'professional_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_students');
    }
};
