<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_allergy', 'allergy_id', 'user_id');
    }

    public function allergicIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_allergy', 'allergy_id', 'ingredient_id');
    }
}
