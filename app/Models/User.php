<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Table & primary key
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Mass assignable fields
    protected $fillable = [
        'vards',
        'email',
        'password',
        'registracijas_datums',
        'pedeja_pieteiksanas',
    ];

    // Hide sensitive fields
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cast fields to appropriate types
    protected $casts = [
        'registracijas_datums' => 'date',
        'pedeja_pieteiksanas'  => 'datetime',
    ];

    // Relationships
    public function dietasIerobezojumi()
    {
        return $this->belongsToMany(
            DietasIerobezojumi::class,
            'dietas_ierobezojumi_user',
            'user_id',
            'dietas_ierobezojumi_id'
        )->with(['restrictedIngredients']);
    }

    public function alergijas()
    {
        return $this->belongsToMany(
            Alergijas::class,
            'alergijas_user',
            'user_id',
            'alergijas_id'
        )->with(['allergicIngredients']);
    }
    
    // Add favorites relationship
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function favoriteRecipes()
    {
        return $this->belongsToMany(
            Recipe::class,
            'favorites',
            'user_id',
            'receptes_id'
        );
    }

    public function getForbiddenIngredientIds()
    {
        $dietIngredients = $this->dietasIerobezojumi
            ->flatMap(fn($diet) => $diet->restrictedIngredients->pluck('id'));
        
        $allergyIngredients = $this->alergijas
            ->flatMap(fn($allergy) => $allergy->allergicIngredients->pluck('id'));
        
        return $dietIngredients->merge($allergyIngredients)->unique()->values()->toArray();
    }
}