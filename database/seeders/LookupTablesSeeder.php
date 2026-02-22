<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupTablesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Lietotājs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrators', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('difficulty_levels')->insert([
            ['name' => 'Viegls', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vidējs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grūts', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('meal_times')->insert([
            ['name' => 'Brokastis', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pusdienas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vakariņas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Piedeva', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deserts', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('nutrition_types')->insert([
            ['name' => 'Olbaltumvielu bagātas receptes', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Veģetāriešiem', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vegāniem', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('diet_types')->insert([
            ['name' => 'Veģetāra', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vegāna', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gaļas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zivis', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('protein_sources')->insert([
            ['name' => 'Olas', 'is_vegetarian' => true, 'is_vegan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jogurts', 'is_vegetarian' => true, 'is_vegan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bez Olbaltumvielām', 'is_vegetarian' => true, 'is_vegan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vistas gaļa', 'is_vegetarian' => false, 'is_vegan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maltā gaļa', 'is_vegetarian' => false, 'is_vegan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zivs', 'is_vegetarian' => false, 'is_vegan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tofu', 'is_vegetarian' => true, 'is_vegan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Makaroni', 'is_vegetarian' => true, 'is_vegan' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('ingredient_categories')->insert([
            ['name' => 'Dārzeņi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Augļi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garšvielas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gaļas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jūras veltes', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cepšanai', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Graudu produkti', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Piena produkti un olas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Eļlas un tauki', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Saldinātāji', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Šķidrumi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rieksti un sēklas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garšaugi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sēnes', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pākšaugi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Saldumi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vīni, alus un degvīni', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mērces un piedevas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Konservēti produkti', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Piena produktu alternatīvas', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('units')->insert([
            ['name' => 'g', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ml', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'gab.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'l', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'tējk.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ēdk.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
