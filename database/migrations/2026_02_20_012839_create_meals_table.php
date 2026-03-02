<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->onDelete('cascade');
            
            $table->string('name'); // Ex: "Café da Manhã", "Almoço"
            $table->time('time')->nullable(); // Horário sugerido
            $table->integer('order')->default(0); // Para ordenar as refeições
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
