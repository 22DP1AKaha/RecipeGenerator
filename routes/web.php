<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home page
Route::get('/', fn() => Inertia::render('Home'))->name('home');

// Recipes listing
Route::get('/receptes', fn() => Inertia::render('Receptes'))->name('receptes');

// AI generation
Route::get('/aireceptes', fn() => Inertia::render('AIReceptes'))->name('aireceptes');

// API route for fetching recipes
Route::get('/api/recipes', [RecipeController::class, 'index']);

// API route for fetching a single recipe
Route::get('/api/recipes/{id}', [RecipeController::class, 'show']);

// Route for viewing a single recipe page
Route::get('/recepte/{id}', fn($id) => Inertia::render('RecepteDyn', ['id' => $id]))
    ->name('recepte');

require __DIR__.'/auth.php';