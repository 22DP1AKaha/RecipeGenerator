<?php

// app/Models/Instruction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    // Link each instruction to a recipe
    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'receptes_id'); // Specify the custom foreign key
    }
}