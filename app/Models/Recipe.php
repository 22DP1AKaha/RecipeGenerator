<?php

// app/Models/Recipe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    // Relationship with instructions
    public function instructions()
    {
        return $this->hasMany(Instruction::class, 'receptes_id'); // Specify the custom foreign key
    }

    // Many-to-many relationship with ingredients
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients', 'receptes_id', 'sastavdalas_id')
                    ->withPivot('daudzums'); // Include pivot table columns
    }
}