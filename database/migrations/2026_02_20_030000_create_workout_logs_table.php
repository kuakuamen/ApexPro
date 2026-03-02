<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->date('date');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Evitar duplicidade: um exercício só pode ser marcado como feito uma vez por dia (simplificação)
            // Ou podemos permitir múltiplas vezes se for séries separadas, mas vamos começar simples.
            $table->unique(['student_id', 'exercise_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
