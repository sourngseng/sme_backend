<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check query parameter e.g. ?lang=km or ?locale=km
        $requestedLocale = $request->query('lang') ?: $request->query('locale');

        // 2. Or check session for web sessions
        if (! $requestedLocale && $request->hasSession()) {
            $requestedLocale = $request->session()->get('locale');
        }

        // 3. Or check Accept-Language header for API requests
        if (! $requestedLocale && $request->hasHeader('Accept-Language')) {
            $headerLocale = substr($request->header('Accept-Language'), 0, 2);
            if (in_array($headerLocale, ['en', 'km'])) {
                $requestedLocale = $headerLocale;
            }
        }

        // Validate and apply locale
        if ($requestedLocale && in_array($requestedLocale, ['en', 'km'])) {
            App::setLocale($requestedLocale);
            if ($request->hasSession()) {
                $request->session()->put('locale', $requestedLocale);
            }
        } else {
            App::setLocale('en');
        }

        return $next($request);
    }
}
