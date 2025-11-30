<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'base64_data',
        'mime_type',
        'original_filename',
        'file_size',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'image_id');
    }

    public function getDataUrlAttribute()
    {
        return "data:{$this->mime_type};base64,{$this->base64_data}";
    }
}
