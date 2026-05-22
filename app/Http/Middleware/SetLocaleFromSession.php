<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', $request->cookie('locale'));

        if (! $locale) {
            // fallback to laravel localization or config
            $locale = class_exists(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::class) 
                ? \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() 
                : config('app.locale');
        }

        if (in_array($locale, ['id', 'en'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
