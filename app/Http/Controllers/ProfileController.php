<?php

namespace App\Http\Controllers;

use App\Models\DietasIerobezojumi;
use App\Models\Alergijas;
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
            'dietasIerobezojumi.restrictedIngredients',
            'alergijas.allergicIngredients'
        ]);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => [
                'vards' => $user->vards,
                'email' => $user->email,
                'forbidden_ingredients' => $user->getForbiddenIngredientIds(),
                'dietas_ierobezojumi' => $user->dietasIerobezojumi->pluck('dietas_ierobezojumi_id'),
                'alergijas' => $user->alergijas->pluck('alergijas_id'),
            ],
            'dietas' => DietasIerobezojumi::all(['dietas_ierobezojumi_id', 'nosaukums']),
            'alergijas' => Alergijas::all(['alergijas_id', 'nosaukums']),
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
        $user->dietasIerobezojumi()->sync($request->input('dietas_ierobezojumi', []));
        $user->alergijas()->sync($request->input('alergijas', []));

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