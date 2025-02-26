<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\RecipeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home page
Route::get('/', function () {
    return Inertia::render('Home');
});

// Receptes page
Route::get('/receptes', function () {
    return Inertia::render('Receptes');
});

// API route for fetching recipes
Route::get('/api/recipes', [RecipeController::class, 'index']);

Route::get('/recepte/{id}', function ($id) {
    return Inertia::render('Recepte', ['id' => $id]);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';