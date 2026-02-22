<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlergijasSeeder extends Seeder
{
    public function run()
    {
        DB::table('allergies')->insert([
            ['name' => 'Rieksti'],
            ['name' => 'Piens'],
            ['name' => 'Olas'],
            ['name' => 'Soja'],
            ['name' => 'Gliemenes'],
            ['name' => 'Kvieši'],
            ['name' => 'Zivis'],
            ['name' => 'Sezama sēklas'],
            ['name' => 'Selerijas'],
        ]);
    }
}
