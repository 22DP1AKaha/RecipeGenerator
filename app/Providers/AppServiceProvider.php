<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

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

        Vite::prefetch(concurrency: 3);
        Inertia::share([
            'auth' => fn() => [
                'user' => auth()->user(),
            ],
        ]);
    }
}
