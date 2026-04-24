<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DietPlan extends Model
{
    protected $fillable = [
        'student_id',
        'nutritionist_id',
        'name',
        'goal',
        'description',
        'anamnesis',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'anamnesis' => 'array',
        'is_active' => 'boolean',
    ];

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class)->orderBy('order');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }
}
