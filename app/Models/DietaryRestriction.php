<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietaryRestriction extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'dietary_restriction_user', 'dietary_restriction_id', 'user_id');
    }

    public function restrictedIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'dietary_restriction_ingredient', 'dietary_restriction_id', 'ingredient_id');
    }
}
