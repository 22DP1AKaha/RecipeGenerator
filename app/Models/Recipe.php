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
        return $this->belongsToMany(
            Ingredient::class,
            'recipe_ingredients',    // pivot table
            'receptes_id',           // this model’s FK on the pivot
            'sastavdalas_id'         // related model’s FK on the pivot
        )
        ->withPivot(['daudzums'])
        ->withTimestamps();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'receptes_id');
    }

    public function userRating()
    {
        return $this->hasOne(Rating::class, 'receptes_id')
            ->where('user_id', auth()->id());
    }

    // Accessors for easy data access
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('vertejums') ?: 0;
    }

    public function getUserRatingAttribute()
    {
        return $this->userRating()->value('vertejums') ?: 0;
    }
}