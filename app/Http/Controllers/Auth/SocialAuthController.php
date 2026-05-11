<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller // Pieslēgšanās caur Google kontu
{
    public function redirect()
    {
        // Aizsūtām uz Google, lai cilvēks tur autorizējas
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        // Atpakaļ no Google ar lietotāja datiem
        $googleUser = Socialite::driver('google')->user();

        // Vai mums jau ir tāds e-pasts datubāzē?
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Ja jā, bet vēl nav saistīts ar Google, sasienam abus kopā
            if (!$user->social_id) {
                $user->update([
                    'social_provider'   => 'google',
                    'social_id'         => $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        } else {
            // Pavisam jauns lietotājs - veidojam no nulles ar standarta lomu
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

        // Iekšā un mājup
        Auth::login($user);

        return redirect()->route('home');
    }
}
