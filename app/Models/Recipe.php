<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cooking_time',
        'difficulty',
        'meal_time',
        'nutrition',
        'diet_type',
        'protein_source',
        'image_id',
        'is_public',
    ];

    public function instructions()
    {
        return $this->hasMany(Instruction::class, 'recipe_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'recipe_ingredients',
            'recipe_id',
            'ingredient_id'
        )
        ->withPivot(['quantity', 'quantity_numeric', 'unit', 'notes'])
        ->withTimestamps();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'recipe_id');
    }

    public function userRating()
    {
        return $this->hasOne(Rating::class, 'recipe_id')
            ->where('user_id', auth()->id());
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'recipe_id');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?: 0;
    }

    public function getUserRatingAttribute()
    {
        return $this->userRating()->value('rating') ?: 0;
    }

    public function isFavoritedByUser()
    {
        if (!auth()->check()) return false;
        return $this->favorites()->where('user_id', auth()->user()->user_id)->exists();
    }
}
