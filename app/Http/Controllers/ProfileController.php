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
    public function edit(Request $request): Response
    {
        // Ielasam lietotāja profilu ar uztura ierobežojumiem un alerģijām
        $user = $request->user()->load([
            'dietaryRestrictions.restrictedIngredients',
            'allergies.allergicIngredients'
        ]);

        // Atgriežam profila rediģēšanas lapu ar visiem nepieciešamajiem datiem
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

        // Aizpildām lietotāja datus ar validētajiem laukiem
        $user->fill($request->validated());

        // Ja e-pasts mainīts, noņemam apstiprinājuma atzīmi
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Saglabājam profila izmaiņas
        $user->save();

        // Sinhronizējam uztura ierobežojumus un alerģijas
        $user->dietaryRestrictions()->sync($request->input('dietas_ierobezojumi', []));
        $user->allergies()->sync($request->input('alergijas', []));

        return Redirect::route('profile.edit')->with('status', 'profils-atjauninats');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Pārbaudām paroli (izņemot Google lietotājiem)
        if (!$user->social_provider) {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        }

        // Izlogoties, dzēšam kontu un invalidējam sesiju
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
