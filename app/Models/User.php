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

    public function dietaryRestrictions()
    {
        return $this->belongsToMany(
            DietaryRestriction::class,
            'dietary_restriction_user',
            'user_id',
            'dietary_restriction_id'
        )->with(['restrictedIngredients']);
    }

    public function allergies()
    {
        return $this->belongsToMany(
            Allergy::class,
            'allergy_user',
            'user_id',
            'allergy_id'
        )->with(['allergicIngredients']);
    }

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
            'recipe_id'
        );
    }

    public function getForbiddenIngredientIds()
    {
        $dietIngredients = $this->dietaryRestrictions
            ->flatMap(fn($diet) => $diet->restrictedIngredients->pluck('id'));

        $allergyIngredients = $this->allergies
            ->flatMap(fn($allergy) => $allergy->allergicIngredients->pluck('id'));

        return $dietIngredients->merge($allergyIngredients)->unique()->values()->toArray();
    }
}