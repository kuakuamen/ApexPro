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
        Schema::table('plan_configs', function (Blueprint $table) {
            $table->string('mp_plan_id')->nullable()->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('plan_configs', function (Blueprint $table) {
            $table->dropColumn('mp_plan_id');
        });
    }
};
