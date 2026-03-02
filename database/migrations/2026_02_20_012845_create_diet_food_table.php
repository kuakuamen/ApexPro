<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained('meals')->onDelete('cascade');
            
            $table->string('name'); // Ex: "Ovos Cozidos"
            $table->string('quantity'); // Ex: "2 unidades" ou "100g"
            $table->string('calories')->nullable(); // Kcal aproximadas
            $table->text('observation')->nullable(); // Ex: "Gema mole opcional"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_foods');
    }
};
