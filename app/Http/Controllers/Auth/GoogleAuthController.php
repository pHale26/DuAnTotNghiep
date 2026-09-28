<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Merge google_id vào tài khoản đã có
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Tạo tài khoản mới từ Google (password ngẫu nhiên vì user sẽ login bằng Google)
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)),
                ]);
            }
        }

        Auth::login($user, remember: true);

        if ($user->role === 'admin' || $user->role === 'staff') {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->role === 'cskh') {
            return redirect()->intended(route('admin.chat.index', absolute: false));
        }

        if ($user->role === 'guide') {
            return redirect()->intended(route('guide.dashboard', absolute: false));
        }

        return redirect()->intended('/');
    }
}
