<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAdmin // Middleware, kas neielaiž svešus admin sadaļās
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role->name !== 'Administrators') {
            abort(403);
        }

        return $next($request);
    }
}
