<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'workout_plan_id')) {
                $table->unsignedBigInteger('workout_plan_id')->nullable()->after('personal_id');
                $table->foreign('workout_plan_id')->references('id')->on('workout_plans')->onDelete('set null');
            }

            if (!Schema::hasColumn('assessments', 'goal')) {
                $table->string('goal')->nullable()->after('workout_plan_id');
            }

            if (!Schema::hasColumn('assessments', 'experience_level')) {
                $table->string('experience_level')->nullable()->after('goal');
            }

            if (!Schema::hasColumn('assessments', 'extra_image_paths')) {
                $table->json('extra_image_paths')->nullable()->after('back_image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'workout_plan_id')) {
                $table->dropForeign(['workout_plan_id']);
                $table->dropColumn('workout_plan_id');
            }
            if (Schema::hasColumn('assessments', 'goal')) {
                $table->dropColumn('goal');
            }
            if (Schema::hasColumn('assessments', 'experience_level')) {
                $table->dropColumn('experience_level');
            }
            if (Schema::hasColumn('assessments', 'extra_image_paths')) {
                $table->dropColumn('extra_image_paths');
            }
        });
    }
};
