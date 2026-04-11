<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'trial' to professional_subscriptions.status
        DB::statement("ALTER TABLE professional_subscriptions MODIFY COLUMN status ENUM('pending','active','trial','overdue','suspended','cancelled') NOT NULL DEFAULT 'pending'");

        // Add 'boleto' to subscription_transactions.payment_method
        DB::statement("ALTER TABLE subscription_transactions MODIFY COLUMN payment_method ENUM('pix','credit_card','boleto') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE professional_subscriptions MODIFY COLUMN status ENUM('pending','active','overdue','suspended','cancelled') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE subscription_transactions MODIFY COLUMN payment_method ENUM('pix','credit_card') NOT NULL");
    }
};
