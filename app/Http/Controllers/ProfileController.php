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

class ProfileController extends Controller // Lietotāja profila iestatījumi
{
    public function edit(Request $request): Response
    {
        // Paņemam lietotāju kopā ar diētām un alerģijām
        $user = $request->user()->load([
            'dietaryRestrictions.restrictedIngredients',
            'allergies.allergicIngredients'
        ]);

        // Sūtām uz frontu visu, kas vajadzīgs profila lapai
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail'  => $user instanceof MustVerifyEmail,
            'status'           => session('status'),
            'user'             => [
                'vards'                => $user->vards,
                'email'                => $user->email,
                'forbidden_ingredients'=> $user->getForbiddenIngredientIds(),
                'dietas_ierobezojumi'  => $user->dietaryRestrictions->pluck('id'),
                'alergijas'            => $user->allergies->pluck('id'),
                'is_google_user'       => (bool) $user->social_provider,
            ],
            'dietas'   => DietaryRestriction::all(['id', 'name']),
            'alergijas'=> Allergy::all(['id', 'name']),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Atjauninām vārdu un e-pastu
        $user->fill($request->validated());

        // Ja mainīja e-pastu tas atkal jāverificē, bet frontā tas ir aizliegts
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Saglabājam
        $user->save();

        // Diētas un alerģijas sync sakārto tabulas
        $user->dietaryRestrictions()->sync($request->input('dietas_ierobezojumi', []));
        $user->allergies()->sync($request->input('alergijas', []));

        return Redirect::route('profile.edit')->with('status', 'profils-atjauninats');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Parastiem lietotājiem pirms dzēšanas jāievada parole. Google lietotājiem tās vienkārši nav
        if (!$user->social_provider) {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        }

        // Iziet, dzēst kontu, sesiju kill, jaunu tokenu un atgriezt uz home
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
