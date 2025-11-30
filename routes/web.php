<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\FavoriteController; // Ensure correct import
use App\Http\Controllers\Api\DeepSeekRecipeController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home page
Route::get('/', fn() => Inertia::render('Home'))->name('home');

// Recipes listing
Route::get('/receptes', fn() => Inertia::render('Receptes'))->name('receptes');

// AI generation
Route::get('/aireceptes', fn() => Inertia::render('AIreceptes'))->name('aireceptes');

// API routes
Route::prefix('api')->group(function () {
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{id}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::get('/recipe-filters', [RecipeController::class, 'getFilters']);
    Route::get('/config', [ConfigController::class, 'getAppConfig']);

    Route::get('/ingredients', [IngredientController::class, 'index']);

    Route::post('/generate-recipe', [DeepSeekRecipeController::class, 'generateRecipe']);
});

// Route for viewing a single recipe page
Route::get('/recepte/{id}', fn($id) => Inertia::render('RecepteDyn', ['id' => $id]))->name('recepte');

// Ratings route
Route::middleware('auth')->post('/ratings', [RatingController::class, 'store']);

// Favorites routes - FIXED: Use fully qualified namespace
Route::middleware('auth')->post('/favorites', [FavoriteController::class, 'store']);
Route::middleware('auth')->delete('/favorites/{recipe}', [FavoriteController::class, 'destroy']);

require __DIR__.'/auth.php';