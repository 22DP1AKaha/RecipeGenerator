<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProteinSource extends Model
{
    protected $fillable = ['name', 'is_vegetarian', 'is_vegan'];

    protected $casts = [
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'protein_source_id');
    }
}
