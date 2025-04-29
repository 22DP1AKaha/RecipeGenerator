<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            IngredientsTableSeeder::class,
            RecipesTableSeeder::class,
            RecipeIngredientTableSeeder::class,
            InstructionTableSeeder::class,
            DietasIerobezojumiSeeder::class,
            AlergijasSeeder::class,
        ]);
    }
}
