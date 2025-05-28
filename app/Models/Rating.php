<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = ['user_id', 'receptes_id', 'vertejums', 'komentars'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'receptes_id', 'id');
    }

    
}
