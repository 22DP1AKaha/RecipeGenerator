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
            'Augļu salāti ar jogurtu' => [
                'Ābols' => '2 gab.',
                'Banāns' => '1 gab.',
                'Kivi' => '1 gab.',
                'Jogurts' => '100g',
                'Medus' => '20g',
                'Kokosriekstu eļļa' => '5g',
            ],
            'Tomātu zupa' => [
                'Tomāts' => '500g',
                'Sīpols' => '1 gab.',
                'Ķiploki' => '2 gab.',
                'Olīveļļa' => '10g',
                'Sāls' => '5g',
                'Pipari' => '2g',
                'Krējums' => '100g',
                'Ūdens' => '240ml',
            ],
            'Burkānu un ingvera zupa' => [
                'Burkāns' => '500g',
                'Sīpols' => '1 gab.',
                'Ingvers' => '10g',
                'Olīveļļa' => '10g',
                'Sāls' => '5g',
                'Pipari' => '2g',
                'Ūdens' => '240ml',
                'Krējums' => '100g',
            ],
            'Kāpostu un kartupeļu zupa' => [
                'Kāposts' => '300g',
                'Kartupeļi' => '300g',
                'Sīpols' => '1 gab.',
                'Ķiploki' => '2 gab.',
                'Olīveļļa' => '10g',
                'Sāls' => '5g',
                'Pipari' => '2g',
                'Laura lapas' => '1g',
                'Ūdens' => '500ml',
            ],
            'Vistas un rīsu zupa' => [
                'Rīsi'          => '100g',
                'Vistas gaļa'   => '200g',
                'Sīpols'        => '1 gab.',
                'Burkāns'       => '1 gab.',
                'Ūdens'         => '1l',
                'Sāls'          => '1g',
                'Pipari'        => '1g'
            ],
            'Lēcu un spinātu sautējums' => [
                'Lēcas'         => '150g',
                'Spināti'       => '100g',
                'Tomāts'        => '2 gab.',
                'Sīpols'        => '1 gab.',
                'Olīveļļa'      => '10g',
                'Ūdens'         => '250ml',
                'Sāls'          => '1g',
                'Pipari'        => '1g'
            ],
            'Spageti ar ķiploku un sviesta mērci' => [
                'Makaroni'      => '200g',  // izmanto kā spageti
                'Ķiploki'       => '3 gab.',
                'Sviests'       => '30g',
                'Sāls'          => '1g',
                'Pipari'        => '1g'
            ],
            'Vistas sautējums ar dārzeņiem' => [
                'Vistas gaļa'   => '200g',
                'Brokolis'      => '150g',
                'Sīpols'        => '1 gab.',
                'Olīveļļa'      => '10g',
                'Sāls'          => '1g',
                'Pipari'        => '1g'
            ],
            'Tofu un dārzeņu karijs' => [
                'Tofu'              => '150g',
                'Burkāns'           => '1 gab.',
                'Cukini'            => '1 gab.',
                'Kokosriekstu piens' => '200ml',  
                'Karijs'            => '1g',
                'Sāls'              => '1g',
                'Pipari'            => '1g'
            ],
            'Pildīta piprika' => [
                'Paprika'           => '1 gab.',
                'Rīsi'              => '100g',
                'Maltā gaļa'        => '150g',  
                'Tomāts'            => '2 gab.',
                'Olīveļļa'          => '10g',
                'Sāls'              => '1g',
                'Pipari'            => '1g'
            ],
            'Grilēta zivs' => [
                'Zivis'             => '1 gab.',
                'Citrons'           => '1/2 gab.',
                'Sviests'           => '20g',
                'Olīveļļa'          => '5g',
                'Sāls'              => '1g',
                'Pipari'            => '1g'
            ],
            'Cepti kartupeļi ar rozmarīnu' => [
                'Kartupeļi'         => '200g',
                'Rozmarīns'         => '1g',
                'Olīveļļa'          => '10g',
                'Sāls'              => '1g',
                'Pipari'            => '1g'
            ],
            'Kāpostu salāti' => [
                'Kāposts'           => '100g',
                'Burkāns'           => '1 gab.', 
                'Jogurts'           => '50g',
                'Sāls'              => '1g',
                'Pipari'            => '1g'
            ],
            'Gurķu un tomātu salāti' => [
                'Gurķis'    => '1 gab.',
                'Tomāts'    => '2 gab.',
                'Olīveļļa'  => '5g',
                'Sāls'      => '1g',
                'Pipari'    => '1g'
            ],
            'Ābolu pīrāgs ar kanēli' => [
                'Ābols'     => '3 gab.',
                'Kanēlis'   => '1g',
                'Cukurs'   => '50g',
                'Milti'     => '200g',
                'Sviests'   => '100g',
                'Olas'      => '2 gab.'
            ],
            'Mango smūtijs' => [
                'Mango'     => '1 gab.',
                'Jogurts'   => '150g',
                'Medus'     => '10g'
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
     