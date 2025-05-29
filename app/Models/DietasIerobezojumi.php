<?php

// app/Models/DietasIerobezojumi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietasIerobezojumi extends Model
{
    protected $table = 'dietas_ierobezojumi';
    protected $primaryKey = 'dietas_ierobezojumi_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nosaukums'];

    public function users()
    {
        return $this->belongsToMany(
            \app\Models\User::class,
            'dietas_ierobezojumi_user', // Pivot table
            'dietas_ierobezojumi_id',    // Foreign key in the pivot table
            'user_id'                    // Related foreign key
        );
    }

    public function restrictedIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'dietas_ierobezojumi_ingredient', 'dietas_ierobezojumi_id', 'ingredient_id');
    }

    
}

