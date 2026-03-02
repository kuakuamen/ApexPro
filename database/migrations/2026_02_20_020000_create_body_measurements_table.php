<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('professional_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->date('date');
            
            // Medidas Básicas
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->decimal('height', 4, 2)->nullable(); // metros
            $table->decimal('body_fat', 4, 1)->nullable(); // %
            $table->decimal('muscle_mass', 4, 1)->nullable(); // kg ou %
            
            // Circunferências (cm)
            $table->decimal('chest', 5, 2)->nullable();
            $table->decimal('left_arm', 5, 2)->nullable();
            $table->decimal('right_arm', 5, 2)->nullable();
            $table->decimal('waist', 5, 2)->nullable();
            $table->decimal('abdomen', 5, 2)->nullable();
            $table->decimal('hips', 5, 2)->nullable();
            $table->decimal('left_thigh', 5, 2)->nullable();
            $table->decimal('right_thigh', 5, 2)->nullable();
            $table->decimal('left_calf', 5, 2)->nullable();
            $table->decimal('right_calf', 5, 2)->nullable();
            
            // Fotos de Comparação (Manual)
            $table->string('front_photo_path')->nullable();
            $table->string('side_photo_path')->nullable();
            $table->string('back_photo_path')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_measurements');
    }
};
