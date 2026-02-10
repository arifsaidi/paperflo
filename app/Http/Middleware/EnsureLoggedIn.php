<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureLoggedIn
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user_type') || !session()->has('user_id')) {
            return redirect()->route('login.form')
                ->with('error', 'Please log in first.');
        }

        return $next($request);
    }
}
