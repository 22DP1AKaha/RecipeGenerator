<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alergijas;
use App\Models\Ingredient;

class AllergyIngredientsSeeder extends Seeder
{
    public function run()
    {
        $allergies = [
            'Rieksti' => [
                'Zemesriekstu sviests', // While technically a legume, often grouped with nuts for allergies
                'Kokosriekstu eļļa',     // Sometimes flagged for nut allergies
                'Kokosriekstu cukurs',
                'Kokosriekstu ūdens',
                'Kokosriekstu piens',
            ],
            'Piens' => [
                'Piens',
                'Siers',
                'Jogurts',
                'Krējums',
                'Biezpiens',
                'Sviests',
                'Kefīrs',
                'Saldais krējums',
                'Siera krems',
            ],
            'Olas' => [
                'Olas',
            ],
            'Soja' => [
                'Tofu',
                'Tempe',
            ],
            'Gliemenes' => [
                // No matching ingredients in your list, unless you add seafood like 'Mīdijas', 'Austeres', etc.
            ],
            'Kvieši' => [
                'Kvieši',
                'Milti',
                'Baltmaize',
                'Rudzu maize',
                'Makaroni',
                'Cepamais pulveris', // Often contains wheat starch or flour as filler
            ],
            'Zivis' => [
                'Zivis',
            ],
            'Sezama sēklas' => [
                // No explicit ingredient, but may be included in unlisted ingredients like "tahini" or certain oils
            ],
            'Selerijas' => [
                // No matching ingredient found
            ],
        ];

        foreach ($allergies as $allergyName => $ingredients) {
            $allergy = Alergijas::where('nosaukums', $allergyName)->first();
            $allergy->allergicIngredients()->sync(
                Ingredient::whereIn('nosaukums', $ingredients)->pluck('id')
            );
        }
    }
}