<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professional_subscriptions', function (Blueprint $table) {
            $table->string('mp_preapproval_id')->nullable()->unique()->after('status');
            $table->string('mp_preapproval_status')->nullable()->after('mp_preapproval_id');
            $table->timestamp('next_billing_at')->nullable()->after('last_paid_at');
        });

        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->string('mp_preapproval_id')->nullable()->index()->after('mp_payment_id');
            $table->string('mp_status_detail')->nullable()->after('failure_reason');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->dropColumn(['mp_preapproval_id', 'mp_status_detail']);
        });

        Schema::table('professional_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['mp_preapproval_id', 'mp_preapproval_status', 'next_billing_at']);
        });
    }
};