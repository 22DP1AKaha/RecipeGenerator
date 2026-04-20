<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LookupTablesSeeder::class,
            AdminUserSeeder::class,
            IngredientsTableSeeder::class,
            RecipesTableSeeder::class,
            RecipeIngredientTableSeeder::class,
            InstructionTableSeeder::class,
            DietasIerobezojumiSeeder::class,
            AlergijasSeeder::class,
            DietRestrictionIngredientsSeeder::class,
            AllergyIngredientsSeeder::class,
        ]);
    }
}
