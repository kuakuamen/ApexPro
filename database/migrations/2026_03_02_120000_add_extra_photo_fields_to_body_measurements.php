<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->string('photo_side_right')->nullable()->after('photo_side');
            $table->string('photo_side_left')->nullable()->after('photo_side_right');
            $table->json('extra_photos')->nullable()->after('photo_side_left');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->dropColumn(['photo_side_right', 'photo_side_left', 'extra_photos']);
        });
    }
};
