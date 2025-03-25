<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home page
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Receptes page
Route::get('/receptes', function () {
    return Inertia::render('Receptes');
})->name('receptes');

// API route for fetching recipes
Route::get('/api/recipes', [RecipeController::class, 'index']);

// API route for fetching a single recipe
Route::get('/api/recipes/{id}', [RecipeController::class, 'show']);

// Route for viewing a single recipe page (using Inertia for dynamic rendering)
Route::get('/recepte/{id}', function ($id) {
    return Inertia::render('RecepteDyn', [
        'id' => $id, // Explicitly pass the id prop
    ]);
});

// Authentication routes
// Login page
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

// If not already defined in your auth.php, add a POST route for login
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

// Register page
Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

// POST route to handle registration submissions
Route::post('/register', [RegisteredUserController::class, 'store']);

// Authenticated routes for profile management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
