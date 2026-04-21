<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupTablesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Lietotājs'],
            ['name' => 'Administrators'],
        ] as $row) {
            DB::table('roles')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }

        foreach ([
            ['name' => 'Viegls'],
            ['name' => 'Vidējs'],
            ['name' => 'Grūts'],
        ] as $row) {
            DB::table('difficulty_levels')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }

        foreach ([
            ['name' => 'Brokastis'],
            ['name' => 'Pusdienas'],
            ['name' => 'Vakariņas'],
            ['name' => 'Piedeva'],
            ['name' => 'Deserts'],
        ] as $row) {
            DB::table('meal_times')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }

        foreach ([
            ['name' => 'Olbaltumvielu bagātas receptes'],
            ['name' => 'Veģetāriešiem'],
            ['name' => 'Vegāniem'],
        ] as $row) {
            DB::table('nutrition_types')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }

        foreach ([
            ['name' => 'Veģetāra'],
            ['name' => 'Vegāna'],
            ['name' => 'Gaļas'],
            ['name' => 'Zivis'],
        ] as $row) {
            DB::table('diet_types')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }

        foreach ([
            ['name' => 'Olas',                  'is_vegetarian' => true,  'is_vegan' => false],
            ['name' => 'Jogurts',               'is_vegetarian' => true,  'is_vegan' => false],
            ['name' => 'Bez Olbaltumvielām',    'is_vegetarian' => true,  'is_vegan' => true],
            ['name' => 'Vistas gaļa',           'is_vegetarian' => false, 'is_vegan' => false],
            ['name' => 'Maltā gaļa',            'is_vegetarian' => false, 'is_vegan' => false],
            ['name' => 'Zivs',                  'is_vegetarian' => false, 'is_vegan' => false],
            ['name' => 'Tofu',                  'is_vegetarian' => true,  'is_vegan' => true],
            ['name' => 'Makaroni',              'is_vegetarian' => true,  'is_vegan' => true],
        ] as $row) {
            DB::table('protein_sources')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }

        foreach ([
            ['name' => 'Dārzeņi'],
            ['name' => 'Augļi'],
            ['name' => 'Garšvielas'],
            ['name' => 'Gaļas'],
            ['name' => 'Jūras veltes'],
            ['name' => 'Cepšanai'],
            ['name' => 'Graudu produkti'],
            ['name' => 'Piena produkti un olas'],
            ['name' => 'Eļlas un tauki'],
            ['name' => 'Saldinātāji'],
            ['name' => 'Šķidrumi'],
            ['name' => 'Rieksti un sēklas'],
            ['name' => 'Garšaugi'],
            ['name' => 'Sēnes'],
            ['name' => 'Pākšaugi'],
            ['name' => 'Saldumi'],
            ['name' => 'Vīni, alus un degvīni'],
            ['name' => 'Mērces un piedevas'],
            ['name' => 'Konservēti produkti'],
            ['name' => 'Piena produktu alternatīvas'],
        ] as $row) {
            DB::table('ingredient_categories')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }

        foreach ([
            ['name' => 'g'],
            ['name' => 'ml'],
            ['name' => 'gab.'],
            ['name' => 'l'],
            ['name' => 'kg'],
            ['name' => 'tējk.'],
            ['name' => 'ēdk.'],
        ] as $row) {
            DB::table('units')->updateOrInsert(['name' => $row['name']], array_merge($row, ['updated_at' => now()]));
        }
    }
}
