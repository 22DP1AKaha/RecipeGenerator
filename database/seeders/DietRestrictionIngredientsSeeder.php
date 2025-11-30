<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DietaryRestriction;
use App\Models\Ingredient;

class DietRestrictionIngredientsSeeder extends Seeder
{
    public function run()
    {
        $diets = [
            'Bezglutēna' => [
                'Kvieši', 'Milti', 'Makaroni',
                'Baltmaize', 'Rudzu maize',
                'Mieži', 'Auzu pārslas'
            ],

            'Bez piena produktiem' => [
                'Siers', 'Piens', 'Jogurts',
                'Krējums', 'Biezpiens',
                'Sviests', 'Kefīrs',
                'Saldais krējums', 'Siera krems'
            ],

            'Veģetāra' => [
                'Vistas gaļa', 'Liellopu gaļa',
                'Zivis', 'Cūkgaļa', 'Jēra gaļa',
                'Vistas desa', 'Vistas fileja',
                'Maltā gaļa'
            ],

            'Vegāna' => [
                'Olas', 'Vistas gaļa', 'Liellopu gaļa',
                'Zivis', 'Cūkgaļa', 'Jēra gaļa',
                'Vistas desa', 'Vistas fileja',
                'Maltā gaļa', 'Siers', 'Piens',
                'Jogurts', 'Krējums', 'Biezpiens',
                'Sviests', 'Kefīrs', 'Saldais krējums',
                'Siera krems', 'Medus', 'Skābais krējums'
            ],

            'Zema ogļhidrātu' => [
                'Rīsi', 'Makaroni', 'Kvinoja',
                'Auzu pārslas', 'Milti', 'Baltmaize',
                'Rudzu maize', 'Griķi', 'Kartupelī',
                'Kukurūza', 'Burkāns', 'Banāns',
                'Vīnogas'
            ],

            'Zema tauku satura' => [
                'Sviests', 'Krējums', 'Biezpiens',
                'Sviests', 'Olīveļļa', 'Augu eļļa',
                'Kokosriekstu eļļa', 'Sēklu eļļa',
                'Rapsu eļļa', 'Zemesriekstu sviests',
                'Linsēklu eļļa', 'Saulespuķu eļļa',
                'Kokosriekstu piens'
            ],

            'Ketogēnā' => [
                'Rīsi', 'Makaroni', 'Kvieši',
                'Milti', 'Baltmaize', 'Rudzu maize',
                'Griķi', 'Kartupelī', 'Kukurūza',
                'Banāns', 'Apelsīns', 'Mandarīns',
                'Ananāss', 'Mango', 'Vīnogas',
                'Burkāns'
            ],

            'Paleo' => [
                // graudi
                'Kvieši', 'Milti', 'Makaroni',
                'Baltmaize', 'Rudzu maize',
                'Mieži', 'Auzu pārslas',
                'Kvinoja', 'Lēcas', 'Griķi',
                // piena produkti
                'Siers', 'Piens', 'Jogurts',
                'Krējums', 'Biezpiens',
                'Sviests', 'Kefīrs',
                'Saldais krējums', 'Siera krems'
            ],

            'Bez cukura' => [
                'Cukurs', 'Medus', 'Kokosriekstu cukurs',
                'Stevija', 'Kļavu sīrups', 'Agaves sīrups',
                'Bērzu sīrups', 'Rīsu sīrups'
            ],
        ];


        foreach ($diets as $dietName => $ingredients) {
            $diet = DietaryRestriction::firstOrCreate(['name' => $dietName]);
            $diet->restrictedIngredients()->sync(
                Ingredient::whereIn('name', $ingredients)->pluck('id')
            );
        }
    }
}