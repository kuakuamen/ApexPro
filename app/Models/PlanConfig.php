<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PlanConfig extends Model
{
    protected $fillable = [
        'plan_id',
        'name',
        'price',
        'max_students',
        'features',
        'is_active',
        'discount_percent',
        'discount_expires_at',
        'color',
    ];

    protected $casts = [
        'features'            => 'array',
        'discount_expires_at' => 'datetime',
        'is_active'           => 'boolean',
    ];

    public function hasActiveDiscount(): bool
    {
        if (empty($this->discount_percent)) {
            return false;
        }

        if (is_null($this->discount_expires_at)) {
            return true;
        }

        return $this->discount_expires_at->isFuture();
    }

    public function effectivePrice(): float
    {
        if ($this->hasActiveDiscount()) {
            return round($this->price * (1 - $this->discount_percent / 100), 2);
        }

        return (float) $this->price;
    }
}
