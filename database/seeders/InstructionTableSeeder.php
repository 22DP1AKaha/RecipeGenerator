<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstructionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get the recipe ID using the recipe name
        $recipeId = DB::table('recipes')->where('nosaukums', 'Omlete ar spinātiem un sieru')->value('id');

        if (!$recipeId) {
            return; // If the recipe does not exist, exit the seeder
        }

        // Instructions for the recipe
        $instructions = [
            "Sakarsē pannu ar augu eļļu uz vidējas uguns.",
            "Sakult olas ar sāli un pipariem.",
            "Pievieno spinātus sakultajām olām un samaisa.",
            "Ielej maisījumu pannā un cep 2-3 minūtes, līdz apakša ir zeltaini brūna.",
            "Pārkaisa ar sarīvētu sieru un turpina cept, līdz siers izkūst.",
            "Saloka omleti uz pusēm un pasniedz siltu."
        ];

        foreach ($instructions as $index => $step) {
            DB::table('instructions')->insert([
                'receptes_id' => $recipeId,
                'solis_numurs' => $index + 1, // Steps are indexed from 1
                'apraksts' => $step,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
