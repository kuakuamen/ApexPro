<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('personal_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('financial_plan_id')->constrained('financial_plans')->onDelete('cascade');
            $table->date('start_date');
            $table->date('due_date');
            $table->enum('periodicity', ['monthly', 'quarterly', 'semiannual', 'annual', 'custom'])->default('monthly');
            $table->unsignedInteger('custom_days')->nullable();
            $table->enum('status', ['active', 'overdue', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_plans');
    }
};
