<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('personal_id')->constrained('users')->onDelete('cascade'); // Quem criou
            
            $table->string('name'); // Ex: "Hipertrofia Fase 1"
            $table->string('goal')->nullable(); // Ex: "Ganho de massa", "Perda de gordura"
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_plans');
    }
};
