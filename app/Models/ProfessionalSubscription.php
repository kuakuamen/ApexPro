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
            'starts_at'    => 'datetime',
            'expires_at'   => 'datetime',
            'grace_until'  => 'datetime',
            'last_paid_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'price'        => 'decimal:2',
        ];
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
        return $this->status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        if ($this->isActive()) {
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
