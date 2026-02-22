<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecipesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('recipes')->delete();
        DB::table('images')->delete();

        $difficulty = DB::table('difficulty_levels')->pluck('id', 'name');
        $mealTime = DB::table('meal_times')->pluck('id', 'name');
        $nutrition = DB::table('nutrition_types')->pluck('id', 'name');
        $dietType = DB::table('diet_types')->pluck('id', 'name');
        $proteinSource = DB::table('protein_sources')->pluck('id', 'name');

        $imageData = [
            'omlete_ar_sieru.jpg' => '/omlete_ar_sieru.jpg',
            'bananu_pankukas.jpg' => '/bananu_pankukas.jpg',
            'abolu_pirags.jpg' => '/abolu_pirags.jpg',
            'auglu_salati.jpg' => '/auglu_salati.jpg',
            'auzu_parslas_ar_aboliem.jpg' => '/auzu_parslas_ar_aboliem.jpg',
            'avakado_tostermaize.jpg' => '/avakado_tostermaize.jpg',
            'burkanu_zupa.jpg' => '/burkanu_zupa.jpg',
            'grilleta_zivs.jpg' => '/grilleta_zivs.jpg',
            'gurku_tomatu_salati.jpg' => '/gurku_tomatu_salati.jpg',
            'kapostu_kartupelu_zupa.jpg' => '/kapostu_kartupelu_zupa.jpg',
            'kapostu_salati_burkani.jpg' => '/kapostu_salati_burkani.jpg',
            'kartupeli_rozmarins.jpg' => '/kartupeli_rozmarins.jpg',
            'lecu_spinatu_sautejums.jpg' => '/lecu_spinatu_sautejums.jpg',
            'mango_smutijs.jpg' => '/mango_smutijs.jpg',
            'pildita_paprika.jpg' => '/pildita_paprika.jpg',
            'spageti_kiploku_merce.jpg' => '/spageti_kiploku_merce.jpg',
            'tofu_karijs.webp' => '/tofu_karijs.webp',
            'tomatu_zupa.jpg' => '/tomatu_zupa.jpg',
            'vistas_risu_zupa.jpg' => '/vistas_risu_zupa.jpg',
            'vistas_sautejums.jpg' => '/vistas_sautejums.jpg',
        ];

        $recipes = [
            [
                'name' => 'Omlete ar spinātiem un sieru',
                'description' => 'Garšīga omlete ar svaigiem spinātiem un sieru.',
                'cooking_time' => 15,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Brokastis'],
                'nutrition_type_id' => $nutrition['Olbaltumvielu bagātas receptes'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Olas'],
                'is_public' => true,
                '_image' => 'omlete_ar_sieru.jpg',
            ],
            [
                'name' => 'Banānu pankūkas',
                'description' => 'Pūkainas pankūkas ar banāniem.',
                'cooking_time' => 20,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Brokastis'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Olas'],
                'is_public' => true,
                '_image' => 'bananu_pankukas.jpg',
            ],
            [
                'name' => 'Auzu pārslas ar āboliem un kanēli',
                'description' => 'Auzu pārslas ar svaigiem āboliem, kanēli un kļavu sīrupu.',
                'cooking_time' => 10,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Brokastis'],
                'nutrition_type_id' => $nutrition['Vegāniem'],
                'diet_type_id' => $dietType['Vegāna'],
                'protein_source_id' => $proteinSource['Bez Olbaltumvielām'],
                'is_public' => true,
                '_image' => 'auzu_parslas_ar_aboliem.jpg',
            ],
            [
                'name' => 'Avakado tostermaize ar olu',
                'description' => 'Tostermaize ar avakado, olu un olīvu eļļu.',
                'cooking_time' => 10,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Brokastis'],
                'nutrition_type_id' => $nutrition['Olbaltumvielu bagātas receptes'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Olas'],
                'is_public' => true,
                '_image' => 'avakado_tostermaize.jpg',
            ],
            [
                'name' => 'Augļu salāti ar jogurtu',
                'description' => 'Svaigu augļu salāti ar jogurtu un medu.',
                'cooking_time' => 10,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Brokastis'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Jogurts'],
                'is_public' => true,
                '_image' => 'auglu_salati.jpg',
            ],
            [
                'name' => 'Tomātu zupa',
                'description' => 'Klasiskā tomātu zupa ar krējumu.',
                'cooking_time' => 30,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Pusdienas'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Bez Olbaltumvielām'],
                'is_public' => true,
                '_image' => 'tomatu_zupa.jpg',
            ],
            [
                'name' => 'Burkānu un ingvera zupa',
                'description' => 'Siltā burkānu zupa ar ingveru.',
                'cooking_time' => 35,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Pusdienas'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Bez Olbaltumvielām'],
                'is_public' => true,
                '_image' => 'burkanu_zupa.jpg',
            ],
            [
                'name' => 'Kāpostu un kartupeļu zupa',
                'description' => 'Vienkārši pagatavojama zupa ar kāpostiem un kartupeļiem.',
                'cooking_time' => 40,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Pusdienas'],
                'nutrition_type_id' => $nutrition['Vegāniem'],
                'diet_type_id' => $dietType['Vegāna'],
                'protein_source_id' => $proteinSource['Bez Olbaltumvielām'],
                'is_public' => true,
                '_image' => 'kapostu_kartupelu_zupa.jpg',
            ],
            [
                'name' => 'Vistas un rīsu zupa',
                'description' => 'Klasiska zupa ar vistas gaļu un rīsiem.',
                'cooking_time' => 45,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Pusdienas'],
                'nutrition_type_id' => $nutrition['Olbaltumvielu bagātas receptes'],
                'diet_type_id' => $dietType['Gaļas'],
                'protein_source_id' => $proteinSource['Vistas gaļa'],
                'is_public' => true,
                '_image' => 'vistas_risu_zupa.jpg',
            ],
            [
                'name' => 'Lēcu un spinātu sautējums',
                'description' => 'Sātīgs sautējums ar lēcām, spinātiem un tomātiem.',
                'cooking_time' => 30,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Pusdienas'],
                'nutrition_type_id' => $nutrition['Vegāniem'],
                'diet_type_id' => $dietType['Vegāna'],
                'protein_source_id' => $proteinSource['Bez Olbaltumvielām'],
                'is_public' => true,
                '_image' => 'lecu_spinatu_sautejums.jpg',
            ],
            [
                'name' => 'Spageti ar ķiploku un sviesta mērci',
                'description' => 'Vienkāršs ēdiens ar spageti, ķiplokiem un sviestu.',
                'cooking_time' => 20,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Vakariņas'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Makaroni'],
                'is_public' => true,
                '_image' => 'spageti_kiploku_merce.jpg',
            ],
            [
                'name' => 'Vistas sautējums ar dārzeņiem',
                'description' => 'Sautējums ar vistas gaļu, brokoļiem, pipariem un sīpoliem.',
                'cooking_time' => 25,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Vakariņas'],
                'nutrition_type_id' => $nutrition['Olbaltumvielu bagātas receptes'],
                'diet_type_id' => $dietType['Gaļas'],
                'protein_source_id' => $proteinSource['Vistas gaļa'],
                'is_public' => true,
                '_image' => 'vistas_sautejums.jpg',
            ],
            [
                'name' => 'Tofu un dārzeņu karijs',
                'description' => 'Garšīgs karijs ar tofu, burkāniem, cukini un kokosriekstu pienu.',
                'cooking_time' => 30,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Vakariņas'],
                'nutrition_type_id' => $nutrition['Olbaltumvielu bagātas receptes'],
                'diet_type_id' => $dietType['Vegāna'],
                'protein_source_id' => $proteinSource['Tofu'],
                'is_public' => true,
                '_image' => 'tofu_karijs.webp',
            ],
            [
                'name' => 'Pildīta piprika',
                'description' => 'Paprika pildīta ar rīsiem, malto gaļu un tomātiem.',
                'cooking_time' => 50,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Vakariņas'],
                'nutrition_type_id' => $nutrition['Olbaltumvielu bagātas receptes'],
                'diet_type_id' => $dietType['Gaļas'],
                'protein_source_id' => $proteinSource['Maltā gaļa'],
                'is_public' => true,
                '_image' => 'pildita_paprika.jpg',
            ],
            [
                'name' => 'Grilēta zivs',
                'description' => 'Grilēta zivs ar aromātisku citronu un sviesta mērci.',
                'cooking_time' => 25,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Vakariņas'],
                'nutrition_type_id' => $nutrition['Olbaltumvielu bagātas receptes'],
                'diet_type_id' => $dietType['Zivis'],
                'protein_source_id' => $proteinSource['Zivs'],
                'is_public' => true,
                '_image' => 'grilleta_zivs.jpg',
            ],
            [
                'name' => 'Cepti kartupeļi ar rozmarīnu',
                'description' => 'Kartupeļi cepti ar rozmarīnu un olīvu eļļu.',
                'cooking_time' => 30,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Piedeva'],
                'nutrition_type_id' => $nutrition['Vegāniem'],
                'diet_type_id' => $dietType['Vegāna'],
                'protein_source_id' => $proteinSource['Bez Olbaltumvielām'],
                'is_public' => true,
                '_image' => 'kartupeli_rozmarins.jpg',
            ],
            [
                'name' => 'Kāpostu salāti',
                'description' => 'Klasiskie kāpostu salāti ar burkāniem un jogurtu.',
                'cooking_time' => 15,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Piedeva'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Jogurts'],
                'is_public' => true,
                '_image' => 'kapostu_salati_burkani.jpg',
            ],
            [
                'name' => 'Gurķu un tomātu salāti',
                'description' => 'Viegli salāti ar gurķiem, tomātiem un olīvu eļļu.',
                'cooking_time' => 10,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Piedeva'],
                'nutrition_type_id' => $nutrition['Vegāniem'],
                'diet_type_id' => $dietType['Vegāna'],
                'protein_source_id' => $proteinSource['Bez Olbaltumvielām'],
                'is_public' => true,
                '_image' => 'gurku_tomatu_salati.jpg',
            ],
            [
                'name' => 'Ābolu pīrāgs ar kanēli',
                'description' => 'Klasiskais ābolu pīrāgs ar kanēli un cukuru.',
                'cooking_time' => 60,
                'difficulty_level_id' => $difficulty['Vidējs'],
                'meal_time_id' => $mealTime['Deserts'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Olas'],
                'is_public' => true,
                '_image' => 'abolu_pirags.jpg',
            ],
            [
                'name' => 'Mango smūtijs',
                'description' => 'Atspirdzinošs smūtijs ar mango, jogurtu un medu.',
                'cooking_time' => 10,
                'difficulty_level_id' => $difficulty['Viegls'],
                'meal_time_id' => $mealTime['Deserts'],
                'nutrition_type_id' => $nutrition['Veģetāriešiem'],
                'diet_type_id' => $dietType['Veģetāra'],
                'protein_source_id' => $proteinSource['Jogurts'],
                'is_public' => true,
                '_image' => 'mango_smutijs.jpg',
            ],
        ];

        foreach ($recipes as $recipeData) {
            $imageFilename = $recipeData['_image'];
            unset($recipeData['_image']);

            $recipeId = DB::table('recipes')->insertGetId($recipeData);

            $filePath = public_path($imageFilename);
            $base64Data = '';
            $fileSize = 0;

            if (file_exists($filePath)) {
                $imageContent = file_get_contents($filePath);
                $base64Data = base64_encode($imageContent);
                $fileSize = filesize($filePath);
            }

            DB::table('images')->insert([
                'recipe_id' => $recipeId,
                'base64_data' => $base64Data,
                'mime_type' => str_ends_with($imageFilename, '.webp') ? 'image/webp' : 'image/jpeg',
                'original_filename' => $imageFilename,
                'file_size' => $fileSize,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
