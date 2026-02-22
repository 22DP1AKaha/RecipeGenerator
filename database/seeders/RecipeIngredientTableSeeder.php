<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeIngredientTableSeeder extends Seeder
{
    public function run()
    {
        $units = DB::table('units')->pluck('id', 'name');

        $recipes = [
            'Omlete ar spinātiem un sieru' => [
                ['name' => 'Olas', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Spināti', 'quantity' => 50, 'unit' => 'g'],
                ['name' => 'Siers', 'quantity' => 30, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Augu eļļa', 'quantity' => 5, 'unit' => 'g'],
            ],
            'Banānu pankūkas' => [
                ['name' => 'Banāns', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Olas', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Milti', 'quantity' => 120, 'unit' => 'g'],
                ['name' => 'Cukurs', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Cepamais pulveris', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Kokosriekstu eļļa', 'quantity' => 5, 'unit' => 'g'],
                ['name' => 'Piens', 'quantity' => 240, 'unit' => 'ml'],
            ],
            'Auzu pārslas ar āboliem un kanēli' => [
                ['name' => 'Auzu pārslas', 'quantity' => 60, 'unit' => 'g'],
                ['name' => 'Ūdens', 'quantity' => 120, 'unit' => 'ml'],
                ['name' => 'Ābols', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Kļavu sīrups', 'quantity' => 20, 'unit' => 'g'],
                ['name' => 'Kanēlis', 'quantity' => 10, 'unit' => 'g'],
            ],
            'Avakado tostermaize ar olu' => [
                ['name' => 'Baltmaize', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Avakado', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Olas', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Olīveļļa', 'quantity' => 5, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Augļu salāti ar jogurtu' => [
                ['name' => 'Ābols', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Banāns', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Kivi', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Jogurts', 'quantity' => 100, 'unit' => 'g'],
                ['name' => 'Medus', 'quantity' => 20, 'unit' => 'g'],
                ['name' => 'Kokosriekstu eļļa', 'quantity' => 5, 'unit' => 'g'],
            ],
            'Tomātu zupa' => [
                ['name' => 'Tomāts', 'quantity' => 500, 'unit' => 'g'],
                ['name' => 'Sīpols', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Ķiploks', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Olīveļļa', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 5, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 2, 'unit' => 'g'],
                ['name' => 'Skābais krējums', 'quantity' => 100, 'unit' => 'g'],
                ['name' => 'Ūdens', 'quantity' => 240, 'unit' => 'ml'],
            ],
            'Burkānu un ingvera zupa' => [
                ['name' => 'Burkāns', 'quantity' => 500, 'unit' => 'g'],
                ['name' => 'Sīpols', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Ingvers', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Olīveļļa', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 5, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 2, 'unit' => 'g'],
                ['name' => 'Ūdens', 'quantity' => 240, 'unit' => 'ml'],
                ['name' => 'Skābais krējums', 'quantity' => 100, 'unit' => 'g'],
            ],
            'Kāpostu un kartupeļu zupa' => [
                ['name' => 'Kāposts', 'quantity' => 300, 'unit' => 'g'],
                ['name' => 'Kartupeļi', 'quantity' => 300, 'unit' => 'g'],
                ['name' => 'Sīpols', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Ķiploki', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Olīveļļa', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 5, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 2, 'unit' => 'g'],
                ['name' => 'Laura lapas', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Ūdens', 'quantity' => 500, 'unit' => 'ml'],
            ],
            'Vistas un rīsu zupa' => [
                ['name' => 'Rīsi', 'quantity' => 100, 'unit' => 'g'],
                ['name' => 'Vistas fileja', 'quantity' => 200, 'unit' => 'g'],
                ['name' => 'Sīpols', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Burkāns', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Ūdens', 'quantity' => 1, 'unit' => 'l'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Lēcu un spinātu sautējums' => [
                ['name' => 'Lēcas', 'quantity' => 150, 'unit' => 'g'],
                ['name' => 'Spināti', 'quantity' => 100, 'unit' => 'g'],
                ['name' => 'Tomāts', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Sīpols', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Olīveļļa', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Ūdens', 'quantity' => 250, 'unit' => 'ml'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Spageti ar ķiploku un sviesta mērci' => [
                ['name' => 'Makaroni', 'quantity' => 200, 'unit' => 'g'],
                ['name' => 'Ķiploki', 'quantity' => 3, 'unit' => 'gab.'],
                ['name' => 'Sviests', 'quantity' => 30, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Vistas sautējums ar dārzeņiem' => [
                ['name' => 'Vistas gaļa', 'quantity' => 200, 'unit' => 'g'],
                ['name' => 'Brokolis', 'quantity' => 150, 'unit' => 'g'],
                ['name' => 'Sīpols', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Olīveļļa', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Tofu un dārzeņu karijs' => [
                ['name' => 'Tofu', 'quantity' => 150, 'unit' => 'g'],
                ['name' => 'Burkāns', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Cukini', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Kokosriekstu piens', 'quantity' => 200, 'unit' => 'ml'],
                ['name' => 'Karijs', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Pildīta piprika' => [
                ['name' => 'Paprika', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Rīsi', 'quantity' => 100, 'unit' => 'g'],
                ['name' => 'Maltā gaļa', 'quantity' => 150, 'unit' => 'g'],
                ['name' => 'Tomāts', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Olīveļļa', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Grilēta zivs' => [
                ['name' => 'Zivis', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Citrons', 'quantity' => 0.5, 'unit' => 'gab.'],
                ['name' => 'Sviests', 'quantity' => 20, 'unit' => 'g'],
                ['name' => 'Olīveļļa', 'quantity' => 5, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Cepti kartupeļi ar rozmarīnu' => [
                ['name' => 'Kartupeļi', 'quantity' => 200, 'unit' => 'g'],
                ['name' => 'Rozmarīns', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Olīveļļa', 'quantity' => 10, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Kāpostu salāti' => [
                ['name' => 'Kāposts', 'quantity' => 100, 'unit' => 'g'],
                ['name' => 'Burkāns', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Jogurts', 'quantity' => 50, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Gurķu un tomātu salāti' => [
                ['name' => 'Gurķis', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Tomāts', 'quantity' => 2, 'unit' => 'gab.'],
                ['name' => 'Olīveļļa', 'quantity' => 5, 'unit' => 'g'],
                ['name' => 'Sāls', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Pipari', 'quantity' => 1, 'unit' => 'g'],
            ],
            'Ābolu pīrāgs ar kanēli' => [
                ['name' => 'Ābols', 'quantity' => 3, 'unit' => 'gab.'],
                ['name' => 'Kanēlis', 'quantity' => 1, 'unit' => 'g'],
                ['name' => 'Cukurs', 'quantity' => 50, 'unit' => 'g'],
                ['name' => 'Milti', 'quantity' => 200, 'unit' => 'g'],
                ['name' => 'Sviests', 'quantity' => 100, 'unit' => 'g'],
                ['name' => 'Olas', 'quantity' => 2, 'unit' => 'gab.'],
            ],
            'Mango smūtijs' => [
                ['name' => 'Mango', 'quantity' => 1, 'unit' => 'gab.'],
                ['name' => 'Jogurts', 'quantity' => 150, 'unit' => 'g'],
                ['name' => 'Medus', 'quantity' => 10, 'unit' => 'g'],
            ],
        ];

        foreach ($recipes as $recipeName => $ingredients) {
            $recipeId = DB::table('recipes')->where('name', $recipeName)->value('id');

            if (!$recipeId) {
                continue;
            }

            foreach ($ingredients as $ingredient) {
                $ingredientId = DB::table('ingredients')->where('name', $ingredient['name'])->value('id');

                if ($ingredientId) {
                    DB::table('recipe_ingredients')->insert([
                        'recipe_id' => $recipeId,
                        'ingredient_id' => $ingredientId,
                        'quantity' => $ingredient['quantity'],
                        'unit_id' => $units[$ingredient['unit']],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
