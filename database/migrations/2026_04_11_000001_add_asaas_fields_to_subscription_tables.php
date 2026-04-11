<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── professional_subscriptions ─────────────────────────────────────────
        Schema::table('professional_subscriptions', function (Blueprint $table) {
            $table->string('asaas_customer_id')->nullable()->after('mp_card_id');
            $table->string('asaas_subscription_id')->nullable()->after('asaas_customer_id');
            $table->timestamp('trial_ends_at')->nullable()->after('asaas_subscription_id');
        });

        // ── subscription_transactions ──────────────────────────────────────────
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->string('asaas_payment_id')->nullable()->after('mp_raw_response');
            $table->json('asaas_raw_response')->nullable()->after('asaas_payment_id');
        });

        // ── users ──────────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('professional_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['asaas_customer_id', 'asaas_subscription_id', 'trial_ends_at']);
        });

        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->dropColumn(['asaas_payment_id', 'asaas_raw_response']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('trial_ends_at');
        });
    }
};
