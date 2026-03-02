<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_day_id')->constrained('workout_days')->onDelete('cascade');
            
            $table->string('name'); // Ex: "Supino Reto"
            $table->string('sets')->nullable(); // Ex: "4"
            $table->string('reps')->nullable(); // Ex: "10-12"
            $table->string('rest_time')->nullable(); // Ex: "60s"
            $table->text('observation')->nullable(); // Ex: "Drop set na última"
            $table->string('video_url')->nullable(); // Link do YouTube/Vimeo
            
            $table->integer('order')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
