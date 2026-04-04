<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_configs', function (Blueprint $table) {
            $table->id();
            $table->string('plan_id')->unique();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->integer('max_students');
            $table->json('features');
            $table->boolean('is_active')->default(true);
            $table->integer('discount_percent')->nullable();
            $table->timestamp('discount_expires_at')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_configs');
    }
};
