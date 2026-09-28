<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\TicketBooking;
use App\Models\TourGuide;
use App\Services\RefundService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function myBookings(): View
    {
        Booking::updateUpcomingTourStatuses();

        $bookings = Booking::with([
            'tour_schedule.tour.tour_images',
            'tour_schedule.tour.primaryImage',
            'tour_schedule.tour.destination',
            'booking_passengers',
            'addons',
            'coupon',
            'payments',
            'refund_request',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Phân loại bookings
        $activeBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_UPCOMING,
            Booking::TOUR_IN_PROGRESS,
            Booking::TOUR_CHECKING_IN,
        ]);

        $pastBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_COMPLETED,
            Booking::TOUR_CANCELLED_ADMIN,
            Booking::TOUR_CANCELLED_CUSTOMER,
        ]);

        $pendingLinkedBookings = Booking::with('tour_schedule.tour')->where('customer_email', Auth::user()->email)->whereNull('user_id')->where('ignored_by_user', false)->get();

        return view('frontend.user.bookings', compact('bookings', 'activeBookings', 'pastBookings', 'pendingLinkedBookings'));
    }

    public function profile(): View
    {
        $user = Auth::user();
        $user->load(['bookings', 'wishlists', 'reviews', 'identity']);

        Booking::updateUpcomingTourStatuses();

        // Load bookings with full relations for the bookings tab
        $bookings = Booking::with([
            'tour_schedule.tour.tour_images',
            'tour_schedule.tour.primaryImage',
            'tour_schedule.tour.destination',
            'booking_passengers',
            'addons',
            'coupon',
            'payments',
            'refund_request',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $activeBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_UPCOMING,
            Booking::TOUR_IN_PROGRESS,
            Booking::TOUR_CHECKING_IN,
        ]);

        $pastBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_COMPLETED,
            Booking::TOUR_CANCELLED_ADMIN,
            Booking::TOUR_CANCELLED_CUSTOMER,
        ]);

        // Load ticket bookings
        $ticketBookings = TicketBooking::with([
            'ticket_option.ticket.ticket_images',
            'ticket_option.ticket.destination',
            'coupon',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Load favorites (saved tours) with tour relations for the saved tours tab
        $wishlists = Favorite::with(['tour.destination', 'tour.tour_images'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $pendingLinkedBookings = Booking::with('tour_schedule.tour')->where('customer_email', Auth::user()->email)->whereNull('user_id')->where('ignored_by_user', false)->get();

        return view('frontend.user.profile', compact('user', 'bookings', 'activeBookings', 'pastBookings', 'wishlists', 'ticketBookings', 'pendingLinkedBookings'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|regex:/^[0-9]{10}$/',
        ], [
            'phone.regex' => 'Số điện thoại phải chứa đúng 10 chữ số.',
        ]);

        Auth::user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin cá nhân thành công.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:10240',
        ], [
            'avatar.required' => 'Vui lòng chọn một hình ảnh.',
            'avatar.image' => 'Tệp được chọn phải là hình ảnh định dạng (jpg, png, jpeg, webp, gif...).',
            'avatar.max' => 'Dung lượng ảnh đại diện không được vượt quá 10MB.',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => '/storage/'.$path]);
        }

        return redirect()->back()->with('success', 'Cập nhật ảnh đại diện thành công.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Cập nhật mật khẩu thành công.');
    }

    public function bookingDetail(int $id): View
    {
        $booking = Booking::with([
            'tour_schedule.tour.destination',
            'tour_schedule.tour.tour_images',
            'booking_passengers',
            'payments',
            'booking_accommodations.room_type.accommodation',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $existingReview = null;
        if ($tour = $booking->tour_schedule?->tour) {
            $existingReview = Review::where('user_id', Auth::id())
                ->where('tour_id', $tour->id)
                ->first();
        }

        return view('frontend.user.booking-detail', compact('booking', 'existingReview'));
    }

    public function cancelBooking(Request $request, int $id): RedirectResponse
    {
        $booking = Booking::with('tour_schedule')->where('user_id', Auth::id())->findOrFail($id);

        if ($booking->booking_status === 'cancelled') {
            return redirect()->back()->with('error', 'Đơn hàng đã được hủy trước đó.');
        }

        $isDeparted = Carbon::parse($booking->tour_schedule->departure_date)->startOfDay()->isPast()
            || in_array($booking->tour_status, ['in_progress', 'checking_in', 'completed', 'closed']);

        if ($isDeparted) {
            return redirect()->back()->with('error', 'Tour đã khởi hành, không thể hủy đơn hàng.');
        }

        $bankData = null;
        if ($booking->paid_amount > 0) {
            $request->validate([
                'bank_name' => 'required_with:bank_account_number|string|max:255',
                'bank_account_name' => 'required_with:bank_account_number|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
            ]);

            $bankData = $request->only(['bank_name', 'bank_account_name', 'bank_account_number']);
        }

        $refundService = app(RefundService::class);
        $refundCalc = $refundService->processUserCancellation($booking, $bankData);

        $msg = 'Đơn hàng đã được hủy thành công.';
        if ($refundCalc['is_refundable']) {
            $msg .= ' Yêu cầu hoàn tiền của bạn đang được chờ xử lý.';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
            'guide_id' => 'nullable|exists:tour_guides,id',
            'guide_rating' => 'nullable|integer|between:1,5',
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'tour_id' => $request->tour_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
                'guide_id' => $request->guide_id,
                'guide_rating' => $request->guide_rating,
            ]
        );

        if ($review->guide_id) {
            $guide = TourGuide::find($review->guide_id);
            if ($guide) {
                $guide->updateKpiScore();
            }
        }

        return redirect()->back()->with('success', 'Gửi đánh giá chuyến đi thành công.');
    }

    public function myWishlists(): View
    {
        $wishlists = Wishlist::with(['tour.destination', 'tour.tour_images'])
            ->where('user_id', Auth::id())
            ->get();

        return view('frontend.user.wishlists', compact('wishlists'));
    }

    public function toggleWishlist(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        $userId = Auth::id();
        $tourId = (int) $request->tour_id;

        $favorite = Favorite::where('user_id', $userId)->where('tour_id', $tourId)->first();

        if ($favorite) {
            Favorite::where('user_id', $userId)->where('tour_id', $tourId)->delete();
            $added = false;
        } else {
            Favorite::create([
                'user_id' => $userId,
                'tour_id' => $tourId,
            ]);
            $added = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $added ? 'Đã thêm vào danh sách yêu thích.' : 'Đã xóa khỏi danh sách yêu thích.',
            ]);
        }

        return redirect()->back()->with(
            'success',
            $added ? 'Đã thêm vào danh sách yêu thích.' : 'Đã xóa khỏi danh sách yêu thích.'
        );
    }

    public function removeWishlist(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        Favorite::where('user_id', Auth::id())
            ->where('tour_id', $request->tour_id)
            ->delete();

        return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
    }

    public function handleBookingLink(Request $request, $id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->customer_email !== Auth::user()->email) {
            abort(403);
        }
        
        if ($request->action === 'accept') {
            $booking->user_id = Auth::id();
            $booking->save();
            \App\Models\TicketBooking::where('booking_id', $booking->id)->update(['user_id' => Auth::id()]);
            return redirect()->back()->with('success', 'Đã liên kết đơn hàng thành công.');
        } elseif ($request->action === 'ignore') {
            $booking->ignored_by_user = true;
            $booking->save();
            return redirect()->back()->with('success', 'Đã bỏ qua đơn hàng này.');
        }
        
        return redirect()->back();
    }
}
