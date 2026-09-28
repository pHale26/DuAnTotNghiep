<?php

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

describe('Locale switching', function () {
    it('saves locale to session and queues a cookie when consent is accepted', function () {
        // Cần có cookie consent=accepted để ghi cookie app_locale
        $response = $this->withCookie('cookie_consent', 'accepted')
            ->get(route('locale.switch', ['locale' => 'en']));

        $response->assertRedirect();
        expect(Session::get('locale'))->toBe('en');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'app_locale');

        // Cookie tồn tại và có giá trị (Laravel mã hóa cookie nên không so sánh plaintext)
        expect($cookie)->not->toBeNull();
        expect($cookie->getValue())->toBeString()->not->toBeEmpty();
    });

    it('saves locale to session only when consent is not given', function () {
        // Không có cookie consent → chỉ lưu session, không ghi cookie app_locale
        $response = $this->get(route('locale.switch', ['locale' => 'en']));

        $response->assertRedirect();
        expect(Session::get('locale'))->toBe('en');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'app_locale');

        expect($cookie)->toBeNull();
    });

    it('saves locale to session only when consent is declined', function () {
        $response = $this->withCookie('cookie_consent', 'declined')
            ->get(route('locale.switch', ['locale' => 'vi']));

        $response->assertRedirect();
        expect(Session::get('locale'))->toBe('vi');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'app_locale');

        expect($cookie)->toBeNull();
    });

    it('rejects an invalid locale', function () {
        $response = $this->get(route('locale.switch', ['locale' => 'fr']));

        $response->assertRedirect();
        expect(Session::has('locale'))->toBeFalse();
    });
});

describe('Currency switching', function () {
    it('saves currency to session and queues a cookie when consent is accepted', function () {
        $response = $this->withCookie('cookie_consent', 'accepted')
            ->get(route('currency.switch', ['currency' => 'USD']));

        $response->assertRedirect();
        expect(Session::get('currency'))->toBe('USD');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'app_currency');

        // Cookie tồn tại và có giá trị (Laravel mã hóa cookie nên không so sánh plaintext)
        expect($cookie)->not->toBeNull();
        expect($cookie->getValue())->toBeString()->not->toBeEmpty();
    });

    it('saves currency to session only when consent is not given', function () {
        $response = $this->get(route('currency.switch', ['currency' => 'USD']));

        $response->assertRedirect();
        expect(Session::get('currency'))->toBe('USD');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'app_currency');

        expect($cookie)->toBeNull();
    });

    it('rejects an invalid currency', function () {
        $response = $this->get(route('currency.switch', ['currency' => 'BTC']));

        $response->assertRedirect();
        expect(Session::has('currency'))->toBeFalse();
    });
});

describe('SetLocale middleware', function () {
    it('restores locale from cookie into session when session is empty', function () {
        $response = $this->withCookie('app_locale', 'zh')->get('/');

        $response->assertOk();
        expect(Session::get('locale'))->toBe('zh');
    });

    it('restores currency from cookie into session when session is empty', function () {
        $response = $this->withCookie('app_currency', 'EUR')->get('/');

        $response->assertOk();
        expect(Session::get('currency'))->toBe('EUR');
    });

    it('does not override session locale with cookie value', function () {
        Session::put('locale', 'en');

        $response = $this->withCookie('app_locale', 'zh')->get('/');

        $response->assertOk();
        expect(Session::get('locale'))->toBe('en');
    });

    it('ignores invalid locale from cookie', function () {
        $response = $this->withCookie('app_locale', 'xx')->get('/');

        $response->assertOk();
        expect(Session::has('locale'))->toBeFalse();
    });
});

describe('Cookie consent', function () {
    it('accept stores cookie_consent=accepted cookie', function () {
        $response = $this->post(route('cookie.consent.accept'));

        $response->assertRedirect();

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'cookie_consent');

        expect($cookie)->not->toBeNull();
        expect($cookie->getValue())->toBeString()->not->toBeEmpty();
    });

    it('decline stores cookie_consent=declined cookie', function () {
        $response = $this->post(route('cookie.consent.decline'));

        $response->assertRedirect();

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'cookie_consent');

        expect($cookie)->not->toBeNull();
        expect($cookie->getValue())->toBeString()->not->toBeEmpty();
    });
});
