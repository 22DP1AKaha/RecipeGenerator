<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Api\DeepSeekRecipeController;
use App\Http\Controllers\Api\IngredientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('Home'))->name('home');
Route::get('/receptes', fn() => Inertia::render('Receptes'))->name('receptes');
Route::get('/aireceptes', fn() => Inertia::render('AIreceptes'))->name('aireceptes');

Route::prefix('api')->group(function () {
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{id}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::get('/recipes/{id}/pdf', [RecipeController::class, 'downloadPdf'])->name('recipes.pdf');
    Route::get('/recipe-filters', [RecipeController::class, 'getFilters']);
    Route::get('/config', [ConfigController::class, 'getAppConfig']);

    Route::get('/ingredients', [IngredientController::class, 'index']);

    Route::post('/generate-recipe', [DeepSeekRecipeController::class, 'generateRecipe']);
});

Route::get('/recepte/{id}', fn($id) => Inertia::render('RecepteDyn', ['id' => $id]))->name('recepte');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{recipe}', [FavoriteController::class, 'destroy']);
});

require __DIR__.'/auth.php';
