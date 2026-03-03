<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'professional_id',
        'date',
        'weight',
        'height',
        'body_fat',
        'muscle_mass',
        'chest',
        'left_arm',
        'right_arm',
        'waist',
        'abdomen',
        'hips',
        'left_thigh',
        'right_thigh',
        'left_calf',
        'right_calf',
        'notes',
        'photo_front',
        'photo_back',
        'photo_side',
        'photo_side_right',
        'photo_side_left',
        'extra_photos',
        'injuries',
        'medications',
        'surgeries',
        'pain_points',
        'habits',
        'goal',
        // Skinfolds
        'subescapular',
        'tricipital',
        'bicipital',
        'toracica',
        'abdominal_fold',
        'axilar_media',
        'suprailiaca',
        'coxa_fold',
        'panturrilha_fold',
        'sum_skinfolds',
        'selected_protocol',
        // Additional circumferences
        'ombro',
        'torax',
        'abdomen_inferior',
        'left_arm_contracted',
        'right_arm_contracted',
        'left_forearm',
        'right_forearm',
        'left_thigh_proximal',
        'left_thigh_medial',
        'left_thigh_distal',
        'right_thigh_proximal',
        'right_thigh_medial',
        'right_thigh_distal',
        // Methods/results
        'guedes_density','guedes_fat_pct','guedes_fat_mass','guedes_lean_mass',
        'pollock3_density','pollock3_fat_pct','pollock3_fat_mass','pollock3_lean_mass',
        'pollock7_density','pollock7_fat_pct','pollock7_fat_mass','pollock7_lean_mass',
    ];

    protected $casts = [
        'date' => 'date',
        'extra_photos' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
