<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'allergy_user', 'allergy_id', 'user_id');
    }

    public function allergicIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'allergy_ingredient', 'allergy_id', 'ingredient_id');
    }
}
