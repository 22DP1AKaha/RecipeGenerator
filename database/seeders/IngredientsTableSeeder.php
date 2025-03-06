<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            // Dārzeņi
            ['nosaukums' => 'Tomāts', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Kartupeļi', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Sīpols', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Burkāns', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Kāposts', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Gurķis', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Paprika', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Brokolis', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Spināti', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Cukini', 'kategorija' => 'Dārzeņi'],
            ['nosaukums' => 'Skābenes', 'kategorija' => 'Dārzeņi'],

            // Augļi
            ['nosaukums' => 'Ābols', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Banāns', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Apelsīns', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Citrons', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Mandarīns', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Ananāss', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Kivi', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Mango', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Bumbieris', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Vīnogas', 'kategorija' => 'Augļi'],
            ['nosaukums' => 'Avakado', 'kategorija' => 'Augļi'],

            // Garšvielas
            ['nosaukums' => 'Sāls', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Pipari', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Ķiploki', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Kumins', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Karijs', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Kanēlis', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Ingvers', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Laura lapas', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Rozmarīns', 'kategorija' => 'Garšvielas'],
            ['nosaukums' => 'Timšāns', 'kategorija' => 'Garšvielas'],

            // Proteīni
            ['nosaukums' => 'Olas', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Vistas gaļa', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Liellopu gaļa', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Zivis', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Tofu', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Tempe', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Cūkgaļa', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Vistas desa', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Jēra gaļa', 'kategorija' => 'Proteīni'],
            ['nosaukums' => 'Vistas fileja', 'kategorija' => 'Proteīni'],

            // Graudu produkti
            ['nosaukums' => 'Rīsi', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Makaroni', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Kvinoja', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Auzu pārslas', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Mieži', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Kvieši', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Baltmaize', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Rudzu maize', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Griķi', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Kukurūza', 'kategorija' => 'Graudu produkti'],
            ['nosaukums' => 'Milti', 'kategorija' => 'Graudu produkti'],

            // Piena produkti
            ['nosaukums' => 'Siers', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Jogurts', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Piens', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Krējums', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Biezpiens', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Sviests', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Kefīrs', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Saldais krējums', 'kategorija' => 'Piena produkti'],
            ['nosaukums' => 'Siera krems', 'kategorija' => 'Piena produkti'],

            // Eļlas un tauki
            ['nosaukums' => 'Augu eļļa', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Olīveļļa', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Kokosriekstu eļļa', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Sēklu eļļa', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Rapsu eļļa', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Oliveļļa', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Zemesriekstu sviests', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Linsēklu eļļa', 'kategorija' => 'Eļlas un tauki'],
            ['nosaukums' => 'Saulespuķu eļļa', 'kategorija' => 'Eļlas un tauki'],

            // Saldinātāji
            ['nosaukums' => 'Cukurs', 'kategorija' => 'Saldinātāji'],
            ['nosaukums' => 'Medus', 'kategorija' => 'Saldinātāji'],
            ['nosaukums' => 'Kokosriekstu cukurs', 'kategorija' => 'Saldinātāji'],
            ['nosaukums' => 'Stevija', 'kategorija' => 'Saldinātāji'],
            ['nosaukums' => 'Kļavu sīrups', 'kategorija' => 'Saldinātāji'],
            ['nosaukums' => 'Agaves sīrups', 'kategorija' => 'Saldinātāji'],
            ['nosaukums' => 'Bērzu sīrups', 'kategorija' => 'Saldinātāji'],
            ['nosaukums' => 'Rīsu sīrups', 'kategorija' => 'Saldinātāji'],

            // Šķidrumi
            ['nosaukums' => 'Ūdens', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Ābolu sula', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Apelsīnu sula', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Tēja', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Kafija', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Kokteiļu sula', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Mineralūdens', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Kombuča', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Kokosriekstu ūdens', 'kategorija' => 'Šķidrumi'],
            ['nosaukums' => 'Vīns', 'kategorija' => 'Šķidrumi'],

            // Citi
            ['nosaukums' => 'Cepamais pulveris', 'kategorija' => 'Citi'],
        ];

        DB::table('ingredients')->insert($ingredients);
    }
}