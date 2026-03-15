<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPlan extends Model
{
    protected $fillable = [
        'student_id',
        'personal_id',
        'financial_plan_id',
        'start_date',
        'due_date',
        'periodicity',
        'custom_days',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date'   => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function personal()
    {
        return $this->belongsTo(User::class, 'personal_id');
    }

    public function financialPlan()
    {
        return $this->belongsTo(FinancialPlan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'active'    => 'Ativo',
            'overdue'   => 'Atrasado',
            'suspended' => 'Suspenso',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'active'    => 'green',
            'overdue'   => 'yellow',
            'suspended' => 'red',
            default     => 'gray',
        };
    }
}
