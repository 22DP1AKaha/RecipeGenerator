<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allergy;
use App\Models\Ingredient;

class AllergyIngredientsSeeder extends Seeder
{
    public function run()
    {
        $allergies = [
            'Rieksti' => [
                'Zemesriekstu sviests',
                'Kokosriekstu eļļa',
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
            ],
            'Kvieši' => [
                'Kvieši',
                'Milti',
                'Baltmaize',
                'Rudzu maize',
                'Makaroni',
                'Cepamais pulveris',
            ],
            'Zivis' => [
                'Zivis',
            ],
            'Sezama sēklas' => [
            ],
            'Selerijas' => [
            ],
        ];

        foreach ($allergies as $allergyName => $ingredients) {
            $allergy = Allergy::where('name', $allergyName)->first();
            $allergy->allergicIngredients()->sync(
                Ingredient::whereIn('name', $ingredients)->pluck('id')
            );
        }
    }
}