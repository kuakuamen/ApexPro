<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialPlan extends Model
{
    protected $fillable = [
        'personal_id',
        'name',
        'description',
        'price',
        'periodicity',
        'custom_days',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price'  => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function personal()
    {
        return $this->belongsTo(User::class, 'personal_id');
    }

    public function studentPlans()
    {
        return $this->hasMany(StudentPlan::class);
    }

    public function periodicityLabel(): string
    {
        return match($this->periodicity) {
            'monthly'    => 'Mensal',
            'quarterly'  => 'Trimestral',
            'semiannual' => 'Semestral',
            'annual'     => 'Anual',
            'custom'     => 'Personalizado ('.$this->custom_days.' dias)',
            default      => $this->periodicity,
        };
    }

    public function nextDueDate(\Carbon\Carbon $from): \Carbon\Carbon
    {
        return match($this->periodicity) {
            'monthly'    => $from->copy()->addMonth(),
            'quarterly'  => $from->copy()->addMonths(3),
            'semiannual' => $from->copy()->addMonths(6),
            'annual'     => $from->copy()->addYear(),
            'custom'     => $from->copy()->addDays($this->custom_days ?? 30),
            default      => $from->copy()->addMonth(),
        };
    }
}
