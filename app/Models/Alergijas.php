<?php

// app/Models/Alergijas.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alergijas extends Model
{
    protected $table = 'alergijas';
    protected $primaryKey = 'alergijas_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nosaukums'];

    public function users()
    {
        return $this->belongsToMany(
            \app\Models\User::class,
            'alergijas_user',            // Pivot table
            'alergijas_id',              // Foreign key in the pivot table
            'user_id'                    // Related foreign key
        );
    }

}
