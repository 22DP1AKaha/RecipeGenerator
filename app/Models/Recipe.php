<?php

// app/Models/Recipe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    // Relationship with instructions
    public function instructions()
    {
        return $this->hasMany(Instruction::class, 'receptes_id');
    }

    // Many-to-many relationship with ingredients
    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'recipe_ingredients',
            'receptes_id',
            'sastavdalas_id'
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

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'receptes_id');
    }

    public function isFavoritedByUser()
    {
        if (!auth()->check()) return false;
        // FIXED: Use correct primary key for user comparison
        return $this->favorites()->where('user_id', auth()->user()->user_id)->exists();
    }
}
