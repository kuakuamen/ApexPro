<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $fillable = [
        'student_id',
        'personal_id',
        'workout_plan_id',
        'front_image_path',
        'side_image_path',
        'back_image_path',
        'extra_image_paths',
        'ai_analysis_data',
        'personal_feedback',
        'status',
        'goal',
        'experience_level',
        'injuries',
        'medications',
        'surgeries',
        'availability_time',
        'frequency',
    ];

    protected $casts = [
        'ai_analysis_data'  => 'array',
        'extra_image_paths' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'personal_id');
    }

    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }
}
