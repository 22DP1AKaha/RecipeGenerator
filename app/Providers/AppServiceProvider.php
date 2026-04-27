<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Apstipriniet savu e-pastu — FOODYML')
                ->greeting('Sveiki, ' . $notifiable->vards . '!')
                ->line('Paldies par reģistrēšanos FOODYML! Lūdzu apstipriniet savu e-pasta adresi, noklikšķinot uz pogas zemāk.')
                ->action('Apstiprināt e-pastu', $url)
                ->line('Saite būs derīga 60 minūtes.')
                ->line('Ja jūs nereģistrējāties FOODYML, ignorējiet šo e-pastu.')
                ->salutation('Ar cieņu, FOODYML komanda');
        });

        // Uzstādām paroles drošības prasības visai lietotnei
        Password::defaults(fn() => Password::min(8)->mixedCase()->numbers());

        Vite::prefetch(concurrency: 3);
        Inertia::share([
            'auth' => fn() => [
                'user' => auth()->user(),
            ],
        ]);
    }
}
