<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const VALID_LOCALES = ['vi', 'en', 'zh'];

    private const VALID_CURRENCIES = ['VND', 'USD', 'EUR', 'CNY'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Session::has('locale')) {
            $cookieLocale = $request->cookie('app_locale');
            if ($cookieLocale && in_array($cookieLocale, self::VALID_LOCALES)) {
                Session::put('locale', $cookieLocale);
            }
        }

        if (! Session::has('currency')) {
            $cookieCurrency = $request->cookie('app_currency');
            if ($cookieCurrency && in_array($cookieCurrency, self::VALID_CURRENCIES)) {
                Session::put('currency', $cookieCurrency);
            }
        }

        App::setLocale(Session::get('locale', config('app.locale')));

        return $next($request);
    }
}
