<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_plan_id',
        'student_id',
        'personal_id',
        'amount',
        'due_date',
        'paid_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'   => 'decimal:2',
            'due_date' => 'date',
            'paid_at'  => 'date',
        ];
    }

    public function studentPlan()
    {
        return $this->belongsTo(StudentPlan::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function personal()
    {
        return $this->belongsTo(User::class, 'personal_id');
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'paid'    => 'Pago',
            'pending' => 'Pendente',
            'overdue' => 'Atrasado',
            default   => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'paid'    => 'green',
            'pending' => 'yellow',
            'overdue' => 'red',
            default   => 'gray',
        };
    }
}
