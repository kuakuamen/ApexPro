<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('professional_subscriptions')->onDelete('set null');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('plan_id');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['pix', 'credit_card']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'refunded', 'in_process', 'charged_back'])->default('pending');
            $table->string('mp_payment_id')->nullable()->index();
            $table->string('mp_external_reference', 36)->unique();
            $table->text('pix_qr_code')->nullable();
            $table->text('pix_qr_code_base64')->nullable();
            $table->timestamp('pix_expires_at')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_brand')->nullable();
            $table->tinyInteger('installments')->default(1);
            $table->string('failure_reason')->nullable();
            $table->json('mp_raw_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_transactions');
    }
};
