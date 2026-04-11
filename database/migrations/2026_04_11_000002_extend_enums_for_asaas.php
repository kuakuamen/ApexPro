<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'trial' to professional_subscriptions.status
        DB::statement("ALTER TABLE professional_subscriptions MODIFY COLUMN status ENUM('pending','active','trial','overdue','suspended','cancelled') NOT NULL DEFAULT 'pending'");

        // payment_method mantém apenas pix e credit_card (boleto é mapeado para pix no webhook)
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE professional_subscriptions MODIFY COLUMN status ENUM('pending','active','overdue','suspended','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
