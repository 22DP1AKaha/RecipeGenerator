<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Recipe;  // Import Recipe model

class Ingredient extends Model
{
    // Many-to-many relationship with recipes
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients', 'sastavdalas_id', 'receptes_id')
                    ->withPivot('daudzums');
    }
}