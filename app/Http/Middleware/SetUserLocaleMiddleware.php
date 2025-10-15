<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (app()->runningInConsole()) {
            return $next($request);
        }

        //check user preference first
        $userPreference = $request->user()?->preferences()?->first()->toArray();
        if ($userPreference && $userPreference['preferences'] && $userPreference['preferences']['language']) {
            app()->setLocale($userPreference['preferences']['language']);
        }

        //check browser settings if no user preference is set
        else if ($browserLocale = $request->header('Accept-Language')) {
            if (in_array($browserLocale, ['en', 'fr', 'es', 'de'])) {
                app()->setLocale($browserLocale);
            }
        }
        
        return $next($request);
    }
}
