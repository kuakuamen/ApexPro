<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionTransaction extends Model
{
    protected $fillable = [
        'subscription_id',
        'user_id',
        'plan_id',
        'amount',
        'payment_method',
        'status',
        'mp_payment_id',
        'mp_preapproval_id',
        'mp_external_reference',
        'pix_qr_code',
        'pix_qr_code_base64',
        'pix_expires_at',
        'card_last_four',
        'card_brand',
        'installments',
        'failure_reason',
        'mp_status_detail',
        'mp_raw_response',
        // Asaas
        'asaas_payment_id',
        'asaas_raw_response',
        // Datas
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'pix_expires_at'     => 'datetime',
            'paid_at'            => 'datetime',
            'refunded_at'        => 'datetime',
            'mp_raw_response'    => 'array',
            'asaas_raw_response' => 'array',
            'amount'             => 'decimal:2',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(ProfessionalSubscription::class, 'subscription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
