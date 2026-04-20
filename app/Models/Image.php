<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'recipe_id',
        'base64_data',
        'mime_type',
        'original_filename',
        'file_size',
    ];

    protected $appends = ['data_url'];

    protected $hidden = ['base64_data'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }

    public function getDataUrlAttribute()
    {
        return "data:{$this->mime_type};base64,{$this->base64_data}";
    }

    public function getBase64DataRawAttribute()
    {
        return base64_decode($this->base64_data);
    }

    public function getUrlAttribute()
    {
        return url("/api/images/{$this->id}");
    }
}
