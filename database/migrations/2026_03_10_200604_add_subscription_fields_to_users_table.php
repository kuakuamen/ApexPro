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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cref')->nullable()->after('profession');
            $table->integer('max_students')->default(0)->after('cref');
            $table->timestamp('subscription_expires_at')->nullable()->after('max_students');
            $table->string('plan_name')->nullable()->after('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cref', 'max_students', 'subscription_expires_at', 'plan_name']);
        });
    }
};
