<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlergijasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert predefined data
        DB::table('alergijas')->insert([
            ['nosaukums' => 'Rieksti'],
            ['nosaukums' => 'Zemesrieksti'],
            ['nosaukums' => 'Piens'],
            ['nosaukums' => 'Olas'],
            ['nosaukums' => 'Soja'],
            ['nosaukums' => 'Gliemenes'],
            ['nosaukums' => 'Kvieši'],
            ['nosaukums' => 'Zivis'],
            ['nosaukums' => 'Sezama sēklas'],
            ['nosaukums' => 'Selerijas'],
        ]);
    }
}
