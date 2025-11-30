<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecipesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('recipes')->delete();
        DB::table('images')->delete();

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

        $imageIds = [];
        foreach ($imageData as $filename => $path) {
            $filePath = public_path($filename);
            $base64Data = '';
            $fileSize = 0;

            if (file_exists($filePath)) {
                $imageContent = file_get_contents($filePath);
                $base64Data = base64_encode($imageContent);
                $fileSize = filesize($filePath);
            }

            $imageId = DB::table('images')->insertGetId([
                'base64_data' => $base64Data,
                'mime_type' => str_ends_with($filename, '.webp') ? 'image/webp' : 'image/jpeg',
                'original_filename' => $filename,
                'file_size' => $fileSize,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $imageIds[$filename] = $imageId;
        }
        

        // Define new recipes
        $recipes = [
            // Brokastis un uzkodas
            [
                'name' => 'Omlete ar spinātiem un sieru',
                'description' => 'Garšīga omlete ar svaigiem spinātiem un sieru.',
                'cooking_time' => 15,
                'difficulty' => 'Viegls',
                'meal_time' => 'Brokastis',
                'nutrition' => 'Olbaltumvielu bagātas receptes',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Olas',
                'image_id' => $imageIds['omlete_ar_sieru.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Banānu pankūkas',
                'description' => 'Pūkainas pankūkas ar banāniem.',
                'cooking_time' => 20,
                'difficulty' => 'Viegls',
                'meal_time' => 'Brokastis',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Olas',
                'image_id' => $imageIds['bananu_pankukas.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Auzu pārslas ar āboliem un kanēli',
                'description' => 'Auzu pārslas ar svaigiem āboliem, kanēli un kļavu sīrupu.',
                'cooking_time' => 10,
                'difficulty' => 'Viegls',
                'meal_time' => 'Brokastis',
                'nutrition' => 'Vegāniem',
                'diet_type' => 'Vegāna',
                'protein_source' => 'Bez Olbaltumvielām',
                'image_id' => $imageIds['auzu_parslas_ar_aboliem.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Avakado tostermaize ar olu',
                'description' => 'Tostermaize ar avakado, olu un olīvu eļļu.',
                'cooking_time' => 10,
                'difficulty' => 'Viegls',
                'meal_time' => 'Brokastis',
                'nutrition' => 'Olbaltumvielu bagātas receptes',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Olas',
                'image_id' => $imageIds['avakado_tostermaize.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Augļu salāti ar jogurtu',
                'description' => 'Svaigu augļu salāti ar jogurtu un medu.',
                'cooking_time' => 10,
                'difficulty' => 'Viegls',
                'meal_time' => 'Brokastis',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Jogurts',
                'image_id' => $imageIds['auglu_salati.jpg'],
                'is_public' => true,
            ],

            // Zupas un sautējumi
            [
                'name' => 'Tomātu zupa',
                'description' => 'Klasiskā tomātu zupa ar krējumu.',
                'cooking_time' => 30,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Pusdienas',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Bez Olbaltumvielām',
                'image_id' => $imageIds['tomatu_zupa.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Burkānu un ingvera zupa',
                'description' => 'Siltā burkānu zupa ar ingveru.',
                'cooking_time' => 35,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Pusdienas',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Bez Olbaltumvielām',
                'image_id' => $imageIds['burkanu_zupa.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Kāpostu un kartupeļu zupa',
                'description' => 'Vienkārši pagatavojama zupa ar kāpostiem un kartupeļiem.',
                'cooking_time' => 40,
                'difficulty' => 'Viegls',
                'meal_time' => 'Pusdienas',
                'nutrition' => 'Vegāniem',
                'diet_type' => 'Vegāna',
                'protein_source' => 'Bez Olbaltumvielām',
                'image_id' => $imageIds['kapostu_kartupelu_zupa.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Vistas un rīsu zupa',
                'description' => 'Klasiska zupa ar vistas gaļu un rīsiem.',
                'cooking_time' => 45,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Pusdienas',
                'nutrition' => 'Olbaltumvielu bagātas receptes',
                'diet_type' => 'Gaļas',
                'protein_source' => 'Vistas gaļa',
                'image_id' => $imageIds['vistas_risu_zupa.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Lēcu un spinātu sautējums',
                'description' => 'Sātīgs sautējums ar lēcām, spinātiem un tomātiem.',
                'cooking_time' => 30,
                'difficulty' => 'Viegls',
                'meal_time' => 'Pusdienas',
                'nutrition' => 'Vegāniem',
                'diet_type' => 'Vegāna',
                'protein_source' => 'Bez Olbaltumvielām',
                'image_id' => $imageIds['lecu_spinatu_sautejums.jpg'],
                'is_public' => true,
            ],

            // Galvenie ēdieni
            [
                'name' => 'Spageti ar ķiploku un sviesta mērci',
                'description' => 'Vienkāršs ēdiens ar spageti, ķiplokiem un sviestu.',
                'cooking_time' => 20,
                'difficulty' => 'Viegls',
                'meal_time' => 'Vakariņas',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Makaroni',
                'image_id' => $imageIds['spageti_kiploku_merce.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Vistas sautējums ar dārzeņiem',
                'description' => 'Sautējums ar vistas gaļu, brokoļiem, pipariem un sīpoliem.',
                'cooking_time' => 25,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Vakariņas',
                'nutrition' => 'Olbaltumvielu bagātas receptes',
                'diet_type' => 'Gaļas',
                'protein_source' => 'Vistas gaļa',
                'image_id' => $imageIds['vistas_sautejums.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Tofu un dārzeņu karijs',
                'description' => 'Garšīgs karijs ar tofu, burkāniem, cukini un kokosriekstu pienu.',
                'cooking_time' => 30,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Vakariņas',
                'nutrition' => 'Olbaltumvielu bagātas receptes',
                'diet_type' => 'Vegāna',
                'protein_source' => 'Tofu',
                'image_id' => $imageIds['tofu_karijs.webp'],
                'is_public' => true,
            ],
            [
                'name' => 'Pildīta piprika',
                'description' => 'Paprika pildīta ar rīsiem, malto gaļu un tomātiem.',
                'cooking_time' => 50,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Vakariņas',
                'nutrition' => 'Olbaltumvielu bagātas receptes',
                'diet_type' => 'Gaļas',
                'protein_source' => 'Maltā gaļa',
                'image_id' => $imageIds['pildita_paprika.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Grilēta zivs',
                'description' => 'Grilēta zivs ar aromātisku citronu un sviesta mērci.',
                'cooking_time' => 25,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Vakariņas',
                'nutrition' => 'Olbaltumvielu bagātas receptes',
                'diet_type' => 'Zivis',
                'protein_source' => 'Zivs',
                'image_id' => $imageIds['grilleta_zivs.jpg'],
                'is_public' => true,
            ],

            // Piedevas
            [
                'name' => 'Cepti kartupeļi ar rozmarīnu',
                'description' => 'Kartupeļi cepti ar rozmarīnu un olīvu eļļu.',
                'cooking_time' => 30,
                'difficulty' => 'Viegls',
                'meal_time' => 'Piedeva',
                'nutrition' => 'Vegāniem',
                'diet_type' => 'Vegāna',
                'protein_source' => 'Bez Olbaltumvielām',
                'image_id' => $imageIds['kartupeli_rozmarins.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Kāpostu salāti',
                'description' => 'Klasiskie kāpostu salāti ar burkāniem un jogurtu.',
                'cooking_time' => 15,
                'difficulty' => 'Viegls',
                'meal_time' => 'Piedeva',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Jogurts',
                'image_id' => $imageIds['kapostu_salati_burkani.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Gurķu un tomātu salāti',
                'description' => 'Viegli salāti ar gurķiem, tomātiem un olīvu eļļu.',
                'cooking_time' => 10,
                'difficulty' => 'Viegls',
                'meal_time' => 'Piedeva',
                'nutrition' => 'Vegāniem',
                'diet_type' => 'Vegāna',
                'protein_source' => 'Bez Olbaltumvielām',
                'image_id' => $imageIds['gurku_tomatu_salati.jpg'],
                'is_public' => true,
            ],

            // Deserti
            [
                'name' => 'Ābolu pīrāgs ar kanēli',
                'description' => 'Klasiskais ābolu pīrāgs ar kanēli un cukuru.',
                'cooking_time' => 60,
                'difficulty' => 'Vidējs',
                'meal_time' => 'Deserts',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Olas',
                'image_id' => $imageIds['abolu_pirags.jpg'],
                'is_public' => true,
            ],
            [
                'name' => 'Mango smūtijs',
                'description' => 'Atspirdzinošs smūtijs ar mango, jogurtu un medu.',
                'cooking_time' => 10,
                'difficulty' => 'Viegls',
                'meal_time' => 'Deserts',
                'nutrition' => 'Veģetāriešiem',
                'diet_type' => 'Veģetāra',
                'protein_source' => 'Jogurts',
                'image_id' => $imageIds['mango_smutijs.jpg'],
                'is_public' => true,
            ],
        ];

        // Insert new recipes
        DB::table('recipes')->insert($recipes);
    }
}