<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DietasIerobezojumiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert predefined data
        DB::table('dietas_ierobezojumi')->insert([
            ['nosaukums' => 'Bezglutēna'],
            ['nosaukums' => 'Veģetāra'],
            ['nosaukums' => 'Vegāna'],
            ['nosaukums' => 'Zema ogļhidrātu'],
            ['nosaukums' => 'Zema tauku satura'],
            ['nosaukums' => 'Bez piena produktiem'],
            ['nosaukums' => 'Ketogēnā'],
            ['nosaukums' => 'Paleo'],
            ['nosaukums' => 'Bez cukura'],
        ]);
    }
}
