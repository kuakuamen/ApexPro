<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('personal_id')->nullable()->constrained('users')->onDelete('set null'); // Quem avaliou

            // Imagens para análise
            $table->string('front_image_path')->nullable();
            $table->string('side_image_path')->nullable();
            $table->string('back_image_path')->nullable();

            // Dados da IA e Validação Humana
            $table->json('ai_analysis_data')->nullable(); // JSON com lordose, escoliose, pontos fracos
            $table->text('personal_feedback')->nullable(); // Texto do personal validando ou corrigindo
            
            $table->enum('status', ['pending_ai', 'pending_review', 'approved', 'rejected'])->default('pending_ai');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
