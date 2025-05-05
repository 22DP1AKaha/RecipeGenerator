<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DietasIerobezojumiUser extends Pivot
{
    protected $table = 'dietas_ierobezojumi_user';
    public $timestamps = true; // Enable timestamps
}