<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->string('photo_front')->nullable();
            $table->string('photo_back')->nullable();
            $table->string('photo_side')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->dropColumn(['photo_front', 'photo_back', 'photo_side']);
        });
    }
};
