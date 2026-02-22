<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name'];

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class, 'unit_id');
    }
}
