<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietaryRestriction extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_dietary_restrictions', 'dietary_restriction_id', 'user_id');
    }

    public function restrictedIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_dietary_restriction', 'dietary_restriction_id', 'ingredient_id');
    }
}
