<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'ingredient_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id');
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients', 'ingredient_id', 'recipe_id')
                    ->withPivot(['quantity', 'unit_id']);
    }

    public function dietaryRestrictions()
    {
        return $this->belongsToMany(DietaryRestriction::class, 'ingredient_dietary_restriction', 'ingredient_id', 'dietary_restriction_id');
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'ingredient_allergy', 'ingredient_id', 'allergy_id');
    }
}
