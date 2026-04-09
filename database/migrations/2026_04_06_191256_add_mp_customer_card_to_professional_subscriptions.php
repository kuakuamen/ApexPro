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
        Schema::table('professional_subscriptions', function (Blueprint $table) {
            $table->string('mp_customer_id')->nullable()->after('mp_preapproval_status');
            $table->string('mp_card_id')->nullable()->after('mp_customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('professional_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['mp_customer_id', 'mp_card_id']);
        });
    }
};
