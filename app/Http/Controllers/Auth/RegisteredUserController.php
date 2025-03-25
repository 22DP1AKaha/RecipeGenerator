<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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
        // Validate the incoming request
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,epasts'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the user with the validated input
        $user = User::create([
            'epasts' => $request->email,
            'parole_hash' => Hash::make($request->password),
            'registracijas_datums' => now()->toDateString(),
        ]);

        event(new Registered($user));

        // Log the user in immediately after registration
        auth()->login($user);

        // Redirect to the login page or the dashboard
        return redirect()->route('home');
    }
}


