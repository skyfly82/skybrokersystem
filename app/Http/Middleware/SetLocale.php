<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session, URL parameter, or use default
        $locale = $request->get('locale') 
            ?? Session::get('locale') 
            ?? config('app.locale', 'pl');

        // Validate locale
        if (in_array($locale, array_keys(config('app.supported_locales', [])))) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}