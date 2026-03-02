<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_plan_id')->constrained('workout_plans')->onDelete('cascade');
            
            $table->string('name'); // Ex: "Treino A - Peito e Tríceps", "Segunda-feira"
            $table->integer('order')->default(0); // Para ordenar os dias
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_days');
    }
};
