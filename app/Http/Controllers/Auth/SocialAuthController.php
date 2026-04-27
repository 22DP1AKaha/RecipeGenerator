<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        // Novirzām lietotāju uz Google autentifikācijas lapu
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        // Iegūstam Google lietotāja datus pēc autentifikācijas
        $googleUser = Socialite::driver('google')->user();

        // Meklējam esošu lietotāju pēc e-pasta
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Piesaistām Google kontu esošajam lietotājam, ja vēl nav piesaistīts
            if (!$user->social_id) {
                $user->update([
                    'social_provider'   => 'google',
                    'social_id'         => $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        } else {
            // Izveidojam jaunu lietotāju ar standarta lomu
            $role = Role::where('name', 'Lietotājs')->first();

            $user = User::create([
                'vards'                => $googleUser->getName(),
                'email'                => $googleUser->getEmail(),
                'password'             => null,
                'social_provider'      => 'google',
                'social_id'            => $googleUser->getId(),
                'role_id'              => $role->id,
                'registracijas_datums' => today(),
                'email_verified_at'    => now(),
            ]);
        }

        // Pieslēdzam lietotāju un novirzām uz sākumlapu
        Auth::login($user);

        return redirect()->route('home');
    }
}
