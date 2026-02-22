<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietType extends Model
{
    protected $fillable = ['name'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'diet_type_id');
    }
}
