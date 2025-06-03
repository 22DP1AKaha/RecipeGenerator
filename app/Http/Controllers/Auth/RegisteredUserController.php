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
        $customMessages = [
            'vards.required' => 'Lūdzu, ievadiet savu vārdu.',
            'email.required' => 'Lūdzu, ievadiet e-pastu.',
            'email.email' => 'Lūdzu, ievadiet derīgu e-pasta adresi.',
            'email.unique' => 'Šis e-pasts jau ir reģistrēts.',
            'password.required' => 'Lūdzu, ievadiet paroli.',
            'password.confirmed' => 'Paroles nesakrīt.',
            'password.min' => 'Parolei jābūt vismaz :min rakstzīmēm.',
            'password.letters' => 'Parolē jābūt vismaz vienam burtam.',
            'password.mixed' => 'Parolē jābūt vismaz vienam lielajam un vienam mazajam burtam.',
            'password.numbers' => 'Parolē jābūt vismaz vienam ciparam.',
            'password.symbols' => 'Parolē jābūt vismaz vienam simbolam.',
            'password.uncompromised' => 'Šī parole ir bijusi datu noplūdē. Lūdzu, izvēlieties citu.',
            'password_confirmation.required' => 'Lūdzu, apstipriniet paroli.',
        ];

        // Validate outside try-catch
        $validated = $request->validate([
            'vards' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required'],
        ], $customMessages);

        try {
            $user = User::create([
                'vards' => $validated['vards'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'registracijas_datums' => now()->toDateString(),
                'pedeja_pieteiksanas' => now(),
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
