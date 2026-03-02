<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    protected $fillable = [
        'student_id',
        'exercise_id',
        'date',
        'completed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'completed_at' => 'datetime',
    ];
}
