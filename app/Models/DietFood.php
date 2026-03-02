<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietFood extends Model
{
    protected $table = 'diet_foods'; // Laravel pode inferir diet_food no singular, então forçamos

    protected $fillable = [
        'meal_id',
        'name',
        'quantity',
        'calories',
        'observation',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
