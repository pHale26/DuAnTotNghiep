<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class CookieConsentController extends Controller
{
    private const CONSENT_ACCEPTED_LIFETIME = 60 * 24 * 365;

    private const CONSENT_DECLINED_LIFETIME = 60 * 24 * 30;

    public function accept(): RedirectResponse
    {
        Cookie::queue('cookie_consent', 'accepted', self::CONSENT_ACCEPTED_LIFETIME);

        return back();
    }

    public function decline(): RedirectResponse
    {
        Cookie::queue('cookie_consent', 'declined', self::CONSENT_DECLINED_LIFETIME);

        return back();
    }
}
