<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeIngredientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $recipes = [
            'Omlete ar spinātiem un sieru' => [
                'Olas' => '2 gab.',
                'Spināti' => '50g',
                'Siers' => '30g',
                'Sāls' => '1g',
                'Pipari' => '1g',
                'Augu eļļa' => '5g',
            ],
            'Banānu pankūkas' => [
                'Banāns' => '2 gab.',
                'Olas' => '1 gab.',
                'Milti' => '120g',
                'Cukurs' => '10g',
                'Sāls' => '1g',
                'Cepamais pulveris' => '10g',
                'Kokosriekstu eļļa' => '5g',
                'Piens' => '240ml',
            ],
            'Auzu pārslas ar āboliem un kanēli' => [
                'Auzu pārslas' => '60g',
                'Ūdens' => '120ml',
                'Ābols' => '1 gab.',
                'Kļavu sīrups' => '20g',
                'Kanēlis' => '10g',
            ],
            'Avakado tostermaize ar olu' => [
                'Baltmaize' => '1 gab.',
                'Avakado' => '1 gab.',
                'Olas' => '1 gab.',
                'Olīveļļa' => '5g',
                'Sāls' => '1g'
            ],
        ];

        foreach ($recipes as $recipeName => $ingredients) {
            // Get the recipe ID from the recipes table
            $recipeId = DB::table('recipes')->where('nosaukums', $recipeName)->value('id');

            if (!$recipeId) {
                // If the recipe doesn't exist, skip it
                continue;
            }

            // Loop through the ingredients for this recipe
            foreach ($ingredients as $ingredientName => $amount) {
                // Get the ingredient ID from the ingredients table
                $ingredientId = DB::table('ingredients')->where('nosaukums', $ingredientName)->value('id');

                if ($ingredientId) {
                    // Insert the ingredient into the recipe_ingredients table
                    DB::table('recipe_ingredients')->insert([
                        'receptes_id' => $recipeId, 
                        'sastavdalas_id' => $ingredientId, 
                        'daudzums' => $amount, 
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
     