<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'is_admin' => $user && $user->role && $user->role->name === 'Administrators',
                'has_favorites' => $user ? $user->favorites()->exists() : false,
                'has_preferences' => $user
                    ? ($user->dietaryRestrictions()->exists() || $user->allergies()->exists())
                    : false,
                'forbidden_ingredients' => Inertia::lazy(fn() => $user
                    ? $user->load(['dietaryRestrictions.restrictedIngredients', 'allergies.allergicIngredients'])
                        ->getForbiddenIngredientIds()
                    : []
                ),
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
