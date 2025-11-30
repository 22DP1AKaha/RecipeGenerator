<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->index('meal_time');
            $table->index('nutrition');
            $table->index('diet_type');
            $table->index('cooking_time');
            $table->index('difficulty');
            $table->index(['is_public', 'created_at']);
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->unique(['recipe_id', 'ingredient_id'], 'recipe_ingredient_unique');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->unique(['user_id', 'recipe_id'], 'user_recipe_rating_unique');
            $table->index('rating');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->unique(['user_id', 'recipe_id'], 'user_recipe_favorite_unique');
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->index(['category', 'name'], 'ingredient_category_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['meal_time']);
            $table->dropIndex(['nutrition']);
            $table->dropIndex(['diet_type']);
            $table->dropIndex(['cooking_time']);
            $table->dropIndex(['difficulty']);
            $table->dropIndex(['is_public', 'created_at']);
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropUnique('recipe_ingredient_unique');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('user_recipe_rating_unique');
            $table->dropIndex(['rating']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('user_recipe_favorite_unique');
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropIndex('ingredient_category_name_index');
        });
    }
};
