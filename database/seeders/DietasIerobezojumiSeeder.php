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
        DB::table('dietary_restrictions')->insert([
            ['name' => 'Bezglutēna'],
            ['name' => 'Veģetāra'],
            ['name' => 'Vegāna'],
            ['name' => 'Zema ogļhidrātu'],
            ['name' => 'Zema tauku satura'],
            ['name' => 'Bez piena produktiem'],
            ['name' => 'Ketogēnā'],
            ['name' => 'Paleo'],
            ['name' => 'Bez cukura'],
        ]);
    }
}
