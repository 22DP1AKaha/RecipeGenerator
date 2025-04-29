<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return inertia('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'vards' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'vards' => $validated['vards'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'registracijas_datums' => now()->toDateString(),
                'pedeja_pieteiksanas' => now(), // initial login time
            ]);

            event(new Registered($user));

            auth()->login($user);

            return redirect()->route('home');
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Radās kļūda reģistrācijas laikā. Lūdzu, mēģiniet vēlreiz.',
            ]);
        }
    }
}
