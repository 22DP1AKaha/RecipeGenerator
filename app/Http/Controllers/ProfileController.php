<?php

namespace App\Http\Controllers;

use App\Models\DietaryRestriction;
use App\Models\Allergy;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user()->load([
            'dietaryRestrictions.restrictedIngredients',
            'allergies.allergicIngredients'
        ]);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => [
                'vards' => $user->vards,
                'email' => $user->email,
                'forbidden_ingredients' => $user->getForbiddenIngredientIds(),
                'dietas_ierobezojumi' => $user->dietaryRestrictions->pluck('id'),
                'alergijas' => $user->allergies->pluck('id'),
            ],
            'dietas' => DietaryRestriction::all(['id', 'name']),
            'alergijas' => Allergy::all(['id', 'name']),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // Now sync will actually pick up the IDs in the request
        $user->dietaryRestrictions()->sync($request->input('dietas_ierobezojumi', []));
        $user->allergies()->sync($request->input('alergijas', []));

        return Redirect::route('profile.edit')->with('status','profils-atjauninats');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}