<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $fillable = [
        'student_id',
        'personal_id',
        'front_image_path',
        'side_image_path',
        'back_image_path',
        'ai_analysis_data',
        'personal_feedback',
        'status',
        'injuries',
        'medications',
        'surgeries',
        'availability_time',
        'frequency',
        'goal',
    ];

    protected $casts = [
        'ai_analysis_data' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'personal_id');
    }
}
