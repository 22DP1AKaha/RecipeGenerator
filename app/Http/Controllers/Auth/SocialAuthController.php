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
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (!$user->social_id) {
                $user->update([
                    'social_provider' => 'google',
                    'social_id'       => $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        } else {
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

        Auth::login($user);

        return redirect()->route('home');
    }
}
