<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutDay extends Model
{
    protected $fillable = [
        'workout_plan_id',
        'name',
        'order',
    ];

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class)->orderBy('order');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }
}
