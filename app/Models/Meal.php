<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    protected $fillable = [
        'diet_plan_id',
        'name',
        'time',
        'order',
    ];

    protected $casts = [
        'time' => 'datetime:H:i',
    ];

    public function foods(): HasMany
    {
        return $this->hasMany(DietFood::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class, 'diet_plan_id');
    }
}
