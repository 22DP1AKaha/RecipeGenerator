<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'category',
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients', 'ingredient_id', 'recipe_id')
                    ->withPivot(['quantity', 'quantity_numeric', 'unit', 'notes']);
    }

    public function dietaryRestrictions()
    {
        return $this->belongsToMany(DietaryRestriction::class, 'dietary_restriction_ingredient', 'ingredient_id', 'dietary_restriction_id');
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'allergy_ingredient', 'ingredient_id', 'allergy_id');
    }
}