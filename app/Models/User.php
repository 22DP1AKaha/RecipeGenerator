<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Specify the table name (optional if it follows Laravel's naming conventions)
    protected $table = 'users';

    // Tell Laravel that `user_id` is the primary key
    protected $primaryKey = 'user_id'; // Corrected the column name for the primary key

    // Laravel expects auto-incremented IDs by default, so we keep this as true
    public $incrementing = true;

    // Define the primary key type
    protected $keyType = 'int';

    // Specify fillable fields to match your users table
    protected $fillable = [
        'epasts',               // Matches `users` table column
        'parole_hash',          // Matches `users` table column
        'registracijas_datums', // Registration date
        'pedeja_pieteiksanas',  // Last login datetime
        'dietas_ierobezojumi',  // Dietary restrictions (JSON)
        'alergijas',            // Allergies (JSON)
    ];

    // Hide sensitive attributes
    protected $hidden = [
        'parole_hash',
        'remember_token',
    ];

    // Define attribute casting for JSON fields
    protected $casts = [
        'pedeja_pieteiksanas' => 'datetime', // Cast to a datetime object
        'dietas_ierobezojumi' => 'array',    // Cast as an array
        'alergijas' => 'array',              // Cast as an array
    ];

    // Optionally, we can define the method for auth identifier
    public function getAuthIdentifierName()
    {
        return 'epasts';  // Use epasts for login, as email is stored in `epasts` column
    }
}
