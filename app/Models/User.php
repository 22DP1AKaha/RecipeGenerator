<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'vards',
        'email',
        'password',
        'role_id',
        'registracijas_datums',
        'pedeja_pieteiksanas',
        'social_provider',
        'social_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'registracijas_datums' => 'date',
        'pedeja_pieteiksanas'  => 'datetime',
        'email_verified_at'    => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function dietaryRestrictions()
    {
        return $this->belongsToMany(
            DietaryRestriction::class,
            'user_dietary_restrictions'
        )->with(['restrictedIngredients']);
    }

    public function allergies()
    {
        return $this->belongsToMany(
            Allergy::class,
            'user_allergy'
        )->with(['allergicIngredients']);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'favorites');
    }

    public function getForbiddenIngredientIds()
    {
        // Ielasam aizliegtās sastāvdaļas no uztura ierobežojumiem
        $dietIngredients = $this->dietaryRestrictions
            ->flatMap(fn($diet) => $diet->restrictedIngredients->pluck('id'));

        // Ielasam aizliegtās sastāvdaļas no alerģijām
        $allergyIngredients = $this->allergies
            ->flatMap(fn($allergy) => $allergy->allergicIngredients->pluck('id'));

        // Apvienojam un atgriežam unikālo ID sarakstu
        return $dietIngredients->merge($allergyIngredients)->unique()->values()->toArray();
    }
}
