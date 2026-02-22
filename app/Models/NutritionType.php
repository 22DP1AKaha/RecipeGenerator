<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionType extends Model
{
    protected $fillable = ['name'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'nutrition_type_id');
    }
}
