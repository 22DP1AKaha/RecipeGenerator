<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DifficultyLevel extends Model
{
    protected $fillable = ['name'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'difficulty_level_id');
    }
}
