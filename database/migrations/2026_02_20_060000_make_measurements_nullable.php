<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->double('weight', 8, 2)->nullable()->change();
            $table->double('height', 8, 2)->nullable()->change();
            $table->double('body_fat', 8, 2)->nullable()->change();
            $table->double('muscle_mass', 8, 2)->nullable()->change();
            $table->double('chest', 8, 2)->nullable()->change();
            $table->double('left_arm', 8, 2)->nullable()->change();
            $table->double('right_arm', 8, 2)->nullable()->change();
            $table->double('waist', 8, 2)->nullable()->change();
            $table->double('abdomen', 8, 2)->nullable()->change();
            $table->double('hips', 8, 2)->nullable()->change();
            $table->double('left_thigh', 8, 2)->nullable()->change();
            $table->double('right_thigh', 8, 2)->nullable()->change();
            $table->double('left_calf', 8, 2)->nullable()->change();
            $table->double('right_calf', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        //
    }
};
