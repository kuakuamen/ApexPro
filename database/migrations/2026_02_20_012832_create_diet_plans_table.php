<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('nutritionist_id')->constrained('users')->onDelete('cascade');
            
            $table->string('name'); // Ex: "Dieta de Cutting"
            $table->string('goal')->nullable(); // Ex: "Perda de gordura"
            $table->text('description')->nullable(); // Recomendações gerais
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};
