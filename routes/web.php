<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Api\DeepSeekRecipeController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Admin\AdminRecipeController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\SocialAuthController;
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
    Route::delete('/ratings/{recipe}', [RatingController::class, 'destroy']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{recipe}', [FavoriteController::class, 'destroy']);
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/receptes', [AdminRecipeController::class, 'index'])->name('admin.recipes.index');
    Route::post('/receptes', [AdminRecipeController::class, 'store'])->name('admin.recipes.store');
    Route::put('/receptes/{recipe}', [AdminRecipeController::class, 'update'])->name('admin.recipes.update');
    Route::delete('/receptes/{recipe}', [AdminRecipeController::class, 'destroy'])->name('admin.recipes.destroy');

    Route::get('/lietotaji', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/lietotaji', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('/lietotaji/{user}/loma', [AdminUserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/lietotaji/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('google.callback');

require __DIR__.'/auth.php';
