<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlergijasUser extends Pivot
{
    protected $table = 'alergijas_user';
    public $timestamps = true; // Enable timestamps
}
