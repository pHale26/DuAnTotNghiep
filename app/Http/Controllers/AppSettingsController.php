<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class AppSettingsController extends Controller
{
    /**
     * Thời gian lưu cookie: 1 năm (tính bằng phút).
     */
    private const COOKIE_LIFETIME_MINUTES = 60 * 24 * 365;

    /**
     * Kiểm tra xem người dùng đã đồng ý sử dụng cookie chưa.
     */
    private function hasCookieConsent(Request $request): bool
    {
        return $request->cookie('cookie_consent') === 'accepted';
    }

    public function switchLocale(Request $request, string $locale): RedirectResponse
    {
        $locales = ['vi', 'en', 'zh'];
        if (in_array($locale, $locales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);

            if ($this->hasCookieConsent($request)) {
                Cookie::queue('app_locale', $locale, self::COOKIE_LIFETIME_MINUTES);
            }
        }

        return back();
    }

    public function switchCurrency(Request $request, string $currency): RedirectResponse
    {
        $currencies = ['VND', 'USD', 'EUR', 'CNY'];
        if (in_array($currency, $currencies)) {
            Session::put('currency', $currency);

            if ($this->hasCookieConsent($request)) {
                Cookie::queue('app_currency', $currency, self::COOKIE_LIFETIME_MINUTES);
            }
        }

        return back();
    }
}
