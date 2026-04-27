<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionAnamnesisSubmission extends Model
{
    protected $fillable = [
        'professional_id',
        'student_id',
        'payload',
        'submitted_by',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

