<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    // Register
    Route::get('registreties', fn() => Inertia::render('Auth/Register'))
         ->name('register');
    Route::post('registreties', [RegisteredUserController::class, 'store'])
         ->name('register.post');

    // Login
    Route::get('ienakt', fn() => Inertia::render('Auth/Login'))
         ->name('login');
    Route::post('ienakt', [AuthenticatedSessionController::class, 'store'])
         ->name('login.post');

    // Password Reset routes (keep these in English as they're less visible)
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Profile route
    Route::get('profils', fn() => Inertia::render('Profile'))
         ->name('profile');

    // Email Verification routes (keep these in English)
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Confirm Password route
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Password Update route
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Logout route
    Route::post('iziet', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');
});