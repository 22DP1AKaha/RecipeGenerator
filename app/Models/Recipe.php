<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cooking_time',
        'difficulty_level_id',
        'meal_time_id',
        'nutrition_type_id',
        'diet_type_id',
        'protein_source_id',
        'is_public',
    ];

    public function difficultyLevel()
    {
        return $this->belongsTo(DifficultyLevel::class);
    }

    public function mealTime()
    {
        return $this->belongsTo(MealTime::class);
    }

    public function nutritionType()
    {
        return $this->belongsTo(NutritionType::class);
    }

    public function dietType()
    {
        return $this->belongsTo(DietType::class);
    }

    public function proteinSource()
    {
        return $this->belongsTo(ProteinSource::class);
    }

    public function instructions()
    {
        return $this->hasMany(Instruction::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot(['quantity', 'unit_id'])
            ->withTimestamps();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function image()
    {
        return $this->hasOne(Image::class);
    }
}
