<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealTime extends Model
{
    protected $fillable = ['name'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'meal_time_id');
    }
}
