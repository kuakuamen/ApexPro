<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalStudent extends Model
{
    protected $table = 'professional_students';

    protected $fillable = [
        'student_id',
        'professional_id',
        'type',
    ];

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
