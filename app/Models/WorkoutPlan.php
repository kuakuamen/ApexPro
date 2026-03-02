<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutPlan extends Model
{
    protected $fillable = [
        'student_id',
        'personal_id',
        'name',
        'goal',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(WorkoutDay::class)->orderBy('order');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'personal_id');
    }
}
