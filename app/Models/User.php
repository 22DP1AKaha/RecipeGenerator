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
            \App\Models\DietasIerobezojumi::class,
            'dietas_ierobezojumi_user',
            'user_id',
            'dietas_ierobezojumi_id'
        );
    }

    public function alergijas()
    {
        return $this->belongsToMany(
            \App\Models\Alergijas::class,
            'alergijas_user',
            'user_id',
            'alergijas_id'
        );
    }
}
