<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ProfessionalSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_name',
        'max_students',
        'price',
        'status',
        'mp_preapproval_id',
        'mp_preapproval_status',
        'mp_customer_id',
        'mp_card_id',
        // Asaas
        'asaas_customer_id',
        'asaas_subscription_id',
        'trial_ends_at',
        // Datas
        'starts_at',
        'expires_at',
        'grace_until',
        'last_payment_method',
        'last_paid_at',
        'next_billing_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'       => 'datetime',
            'expires_at'      => 'datetime',
            'grace_until'     => 'datetime',
            'last_paid_at'    => 'datetime',
            'next_billing_at' => 'datetime',
            'trial_ends_at'   => 'datetime',
            'price'           => 'decimal:2',
        ];
    }

    public function isInTrial(): bool
    {
        return $this->status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SubscriptionTransaction::class, 'subscription_id');
    }

    public function isActive(): bool
    {
        if ($this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture()) {
            return true;
        }
        return $this->status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }

    public function isInProcessingWindow(): bool
    {
        if (!in_array($this->status, ['active', 'overdue'], true)) {
            return false;
        }

        if (!$this->expires_at || !$this->expires_at->isPast()) {
            return false;
        }

        if (empty($this->mp_preapproval_id) && empty($this->asaas_subscription_id)) {
            return false;
        }

        $hours = $this->asaas_subscription_id
            ? max(0, (int) config('services.asaas.processing_window_hours', 0))
            : max(0, (int) config('services.mercadopago.processing_window_hours', 0));

        if ($hours <= 0) {
            return false;
        }

        return Carbon::now()->lte($this->expires_at->copy()->addHours($hours));
    }

    public function canAccessPlatform(): bool
    {
        if (in_array($this->status, ['suspended', 'overdue', 'cancelled'], true)) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isFuture()) {
            return true;
        }

        return $this->isInProcessingWindow();
    }

    public function isInGrace(): bool
    {
        return $this->isInProcessingWindow();
    }

    public function isExpired(): bool
    {
        if ($this->canAccessPlatform()) {
            return false;
        }

        return in_array($this->status, ['overdue', 'suspended', 'cancelled'])
            || ($this->expires_at && $this->expires_at->isPast());
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Pendente',
            'active'    => 'Ativo',
            'overdue'   => 'Vencido',
            'suspended' => 'Suspenso',
            'cancelled' => 'Cancelado',
            default     => $this->status,
        };
    }
}