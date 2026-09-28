<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Booking;
use App\Models\TicketBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function create(Request $request): View
    {
        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }

        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->storeRedirectUrl($request);

        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Tự động nhận diện và gán các đơn hàng khách vãng lai (Guest) cho tài khoản này nếu trùng email
        // Dùng transaction và whereNull lock để chống Race Condition khi 2 request login đồng thời
        DB::transaction(function () use ($user) {
            $bookingIds = Booking::whereNull('user_id')
                ->where('customer_email', $user->email)
                ->lockForUpdate()
                ->pluck('id');

            if ($bookingIds->isNotEmpty()) {
                Booking::whereIn('id', $bookingIds)->update(['user_id' => $user->id]);
                TicketBooking::whereIn('booking_id', $bookingIds)->update(['user_id' => $user->id]);
            }
        });

        if ($user->role === 'admin' || $user->role === 'staff') {
            session()->forget('url.intended');

            return redirect()->route('admin.dashboard');
        }

        if (Auth::user()->role === 'cskh') {
            session()->forget('url.intended');

            return redirect()->route('admin.chat.index');
        }

        if (Auth::user()->role === 'guide') {
            session()->forget('url.intended');

            return redirect()->route('guide.dashboard');
        }

        return redirect()->intended('/');
    }

    /**
     * Lưu lại URL người dùng đang đứng trước khi đăng nhập.
     */
    private function storeRedirectUrl(Request $request): void
    {
        $redirectUrl = $request->query('redirect') ?? $request->input('redirect');

        if (! $redirectUrl) {
            return;
        }

        $appUrl = url('/');

        if (
            Str::startsWith($redirectUrl, $appUrl) ||
            Str::startsWith($redirectUrl, '/')
        ) {
            session(['url.intended' => $redirectUrl]);
        }
    }

    /**
     * Đăng xuất.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_seen_at = null;
            $user->saveQuietly();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
