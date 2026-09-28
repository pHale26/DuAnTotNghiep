<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreTourBookingRequest;
use App\Mail\TourBookingMail;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Holiday;
use App\Models\Payment;
use App\Models\TicketBooking;
use App\Models\TourSchedule;
use App\Models\User;
use App\Notifications\AdminBookingNotification;
use App\Services\FlightBookingService;
use App\Services\TourBookingService;
use App\Services\VnPayService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class TourBookingController extends Controller
{
    public function __construct(
        protected TourBookingService $bookingService,
        protected FlightBookingService $flightService,
        protected VnPayService $vnPayService
    ) {}

    public function store(StoreTourBookingRequest $request)
    {

        $user = Auth::user();
        
        // Layer 2: Không auto-link user. Chỉ ghi chú biến $linkedUser để truyền vào mail nếu email trùng.
        $linkedUser = false;
        if (!$user && $request->filled('customer_email')) {
            $existingUser = User::where('email', $request->customer_email)->first();
            if ($existingUser) {
                // Không gán $user = $existingUser; để đơn hàng vẫn là Guest (user_id = null)
                $linkedUser = true;
            }
        }
        
        $sessionId = session()->getId();

        try {

            $booking = $this->bookingService->createBooking($request->validated(), $user, $sessionId);
            $schedule = $booking->tour_schedule;

            // Bắn thông báo cho Admin
            $admins = Role::where('name', 'Admin')->exists() ? User::role('Admin')->get() : collect();
            if ($admins->count() > 0) {
                Notification::send($admins, new AdminBookingNotification(
                    $booking,
                    'booking_created',
                    'Khách hàng '.$request->customer_name.' vừa đặt tour mới: '.($schedule->tour->title ?? '')
                ));
            }

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Lỗi đặt tour: '.$e->getMessage());

            return redirect()->back()->with('error', $e->getMessage() ?: 'Đã có lỗi xảy ra trong quá trình đặt tour. Vui lòng thử lại.');
        }

        // Layer 1: Nếu là Guest và thanh toán online hoặc giá trị lớn, bắt xác thực email
        if (!$user && ($request->payment_method === 'vnpay' || $booking->total_price > 5000000)) {
            $booking->email_verify_token = rand(100000, 999999);
            $booking->is_email_verified = false;
            $booking->save();

            try {
                Mail::to($request->customer_email)->send(new \App\Mail\TourBookingOtpMail($booking));
            } catch (Exception $e) {
                Log::error('Lỗi gửi OTP email: '.$e->getMessage());
            }

            return redirect()->route('frontend.tours.verify_email', $booking->id);
        }

        try {
            Mail::to($request->customer_email)->send(
                new TourBookingMail($booking, $schedule, $request->customer_name, $request->customer_phone, $linkedUser)
            );
        } catch (Exception $e) {
            Log::error('Lỗi gửi mail đặt tour: '.$e->getMessage());
        }

        if ($request->payment_method === 'vnpay') {
            $vnpayUrl = $this->vnPayService->generateUrl($booking, $request->ip());

            return redirect()->away($vnpayUrl);
        }

        // Nếu thanh toán tiền mặt (COD), tiến hành xuất vé máy bay nếu chọn máy bay
        if ($booking->transport_type === 'flight') {
            $this->flightService->bookFlightForBooking($booking);
        }

        if ($request->transport_type === 'flight') {
            return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Đặt tour và vé máy bay thành công. Vui lòng thanh toán sớm để giữ chỗ.');
        }

        if ($request->transport_type === 'bus') {
            return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Đặt tour thành công. Chúng tôi sẽ liên hệ sớm để xác nhận chuyến xe.');
        }

        return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Đặt tour thành công. Bạn tự túc phương tiện di chuyển.');
    }

    public function bookingSuccess($id)
    {
        $booking = Booking::with(['tour_schedule.tour'])->findOrFail($id);

        if ($booking->user_id && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.tours.booking_success', compact('booking'));
    }

    public function checkStatus($id)
    {
        $booking = Booking::with('tour_schedule')->findOrFail($id);

        if ($booking->user_id && $booking->user_id !== Auth::id() && ! (Auth::check() && Auth::user()->hasAnyRole(['Admin', 'Staff', 'cskh']))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'booking_status' => $booking->booking_status,
            'payment_status' => $booking->payment_status,
            'tour_status' => $booking->tour_status,
            'cancel_reason' => $booking->cancel_reason,
            'available_seats' => $booking->tour_schedule ? $booking->tour_schedule->available_seats : 0,
        ]);
    }

    public function verifyEmail($id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->is_email_verified || $booking->user_id) {
            return redirect()->route('frontend.tours.booking_success', $booking->id);
        }
        
        return view('frontend.tours.verify_email', compact('booking'));
    }

    public function submitVerifyEmail(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        $request->validate(['otp' => 'required|string']);
        
        if ($booking->email_verify_token !== $request->otp) {
            return back()->with('error', 'Mã xác nhận không chính xác.');
        }
        
        $booking->is_email_verified = true;
        $booking->email_verify_token = null;
        $booking->save();
        
        $linkedUser = User::where('email', $booking->customer_email)->exists();
        
        try {
            Mail::to($booking->customer_email)->send(
                new TourBookingMail($booking, $booking->tour_schedule, $booking->customer_name, $booking->customer_phone, $linkedUser)
            );
        } catch (Exception $e) {
            Log::error('Lỗi gửi mail đặt tour sau OTP: '.$e->getMessage());
        }
        
        if ($booking->payment_method === 'vnpay') {
            $vnpayUrl = $this->vnPayService->generateUrl($booking, $request->ip());
            return redirect()->away($vnpayUrl);
        }
        
        if ($booking->transport_type === 'flight') {
            $this->flightService->bookFlightForBooking($booking);
            return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Xác thực thành công. Đặt tour và vé máy bay thành công.');
        }
        
        return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Xác thực thành công.');
    }

    public function createAccount(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        if ($booking->user_id) {
            return redirect()->back()->with('error', 'Đơn hàng này đã được liên kết với một tài khoản.');
        }

        $email = $request->input('email');
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Email không hợp lệ.');
        }

        $password = $request->input('password');
        if (! $password || strlen($password) < 6) {
            return redirect()->back()->with('error', 'Mật khẩu phải có ít nhất 6 ký tự.');
        }

        // Để chống Account Enumeration, chúng ta xử lý âm thầm và luôn trả về cùng một thông báo
        $user = User::where('email', $email)->first();
        // Yêu cầu đăng nhập ở bước tiếp theo.
        if (! $user) {
            $user = User::create([
                'name' => $request->input('name', 'Customer'),
                'email' => $email,
                'phone' => $request->input('phone', ''),
                'password' => Hash::make($password),
            ]);
            $user->assignRole('Customer');

            // Liên kết booking
            $booking->user_id = $user->id;
            $booking->save();
            TicketBooking::where('booking_id', $booking->id)->update(['user_id' => $user->id]);
        }

        // Tuyệt đối không Auto-login để chặn Squatting có quyền truy cập thay đổi dữ liệu
        // Redirect ra trang login với thông báo Generic
        return redirect()->route('login')->with('info', 'Vui lòng đăng nhập để hệ thống bảo mật có thể xác thực và gán đơn hàng vào tài khoản của bạn. Nếu bạn quên mật khẩu, hãy sử dụng tính năng Quên mật khẩu.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:tour_schedules,id',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
        ]);

        $schedule = TourSchedule::with(['tour.tickets.ticket_options', 'tour.addons', 'tour.accommodation_tiers.room_type.accommodation'])->findOrFail($request->schedule_id);

        if ($schedule->status !== 'available' || Carbon::parse($schedule->departure_date)->lt(Carbon::today()->addDays(3))) {
            return redirect()->back()->with('error', 'Tour khởi hành trong vòng 3 ngày tới không thể đặt trực tuyến. Vui lòng chọn lịch trình khác.');
        }
        $totalPersons = $request->adults + $request->children;

        // Cơ chế giữ chỗ (Seat Hold) qua Cache (15 phút)
        $holdKey = "tour_schedule_{$schedule->id}_holds";
        $currentHolds = Cache::get($holdKey, []);

        // Dọn dẹp holds hết hạn
        $currentHolds = array_filter($currentHolds, function ($h) {
            return $h['expires_at'] > now()->timestamp;
        });

        // Tính tổng chỗ đang bị giữ bởi những người khác
        if (! Auth::check()) {
            session()->put('seat_hold_active', true); // Force session save to persist session ID for guests across F5
        }
        $userId = Auth::id() ?? session()->getId();
        $otherHolds = array_filter($currentHolds, function ($h, $k) use ($userId) {
            return $k !== $userId;
        }, ARRAY_FILTER_USE_BOTH);

        $totalHeldByOthers = array_sum(array_column($otherHolds, 'seats'));

        if ($schedule->available_seats - $totalHeldByOthers < $totalPersons) {
            return redirect()->back()->with('error', 'Tour đang có người khác giữ chỗ đang thanh toán. Vui lòng thử lại sau ít phút.');
        }

        // Đăng ký giữ chỗ cho user hiện tại
        $existingHold = $currentHolds[$userId] ?? null;

        if ($existingHold && $existingHold['expires_at'] > now()->timestamp) {
            $currentHolds[$userId] = [
                'seats' => $totalPersons,
                'expires_at' => $existingHold['expires_at'], // Giữ nguyên thời gian hết hạn cũ để tránh F5 gia hạn
            ];
        } else {
            $currentHolds[$userId] = [
                'seats' => $totalPersons,
                'expires_at' => now()->addMinutes(5)->timestamp,
            ];
        }

        $maxExpiresAt = max(array_column($currentHolds, 'expires_at'));
        Cache::put($holdKey, $currentHolds, Carbon::createFromTimestamp($maxExpiresAt));

        // Nếu available_seats thực tế (ko tính hold) không đủ thì cũng báo lỗi
        if ($schedule->available_seats < $totalPersons) {
            return redirect()->back()->with('error', 'Tour không còn đủ chỗ trống cho số lượng hành khách này. Vui lòng chọn ngày khác.');
        }

        $holidaySurcharge = Holiday::getIncreasePercentage($schedule->departure_date);

        $tour = $schedule->tour;
        $costTransport = $tour->cost_transport ?? 0;
        $costMeal = $tour->cost_meal ?? 0;
        $costInsurance = $tour->cost_insurance ?? 0;
        $costServiceFee = $tour->cost_service_fee ?? 0;

        $ticketAdultCost = 0;
        $ticketChildCost = 0;
        foreach ($tour->tickets as $ticket) {
            $ticketAdultCost += $ticket->adult_price ?? 0;
            $ticketChildCost += $ticket->child_price ?? 0;
        }

        $baseCostSum = $costTransport + $costMeal + $costInsurance + $costServiceFee;
        if ($baseCostSum <= 0 && ($tour->base_price ?? 0) > 0) {
            $baseCostSum = max(0, $tour->base_price - $ticketAdultCost);
        }

        $childRate = config('booking.child_price_rate', 0.7);
        $basePrice = $baseCostSum + $ticketAdultCost;
        $childPrice = ($baseCostSum * $childRate) + $ticketChildCost;

        if ($holidaySurcharge > 0) {
            $basePrice = $basePrice * (1 + $holidaySurcharge / 100);
            $childPrice = $childPrice * (1 + $holidaySurcharge / 100);
        }

        // Note: Total price here is without accommodation. Frontend will recalculate.
        $totalPrice = ($basePrice * $request->adults) + ($childPrice * $request->children);

        $user = Auth::user();
        $identity = null;
        if ($user) {
            $user->load('identity');
            $identity = $user->identity;
        }

        $holidays = Holiday::all(['start_date', 'end_date', 'price_increase_percentage']);
        $tourCategoryIds = $schedule->tour->categories->pluck('id')->toArray();

        $coupons = Coupon::where(function ($query) {
            $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
        })
            ->where(function ($query) {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where(function ($query) use ($tourCategoryIds) {
                $query->whereNull('category_id')
                    ->orWhereIn('category_id', $tourCategoryIds);
            })
            ->get();

        $remainingSeconds = max(0, $currentHolds[$userId]['expires_at'] - now()->timestamp);

        return view('frontend.tours.checkout', [
            'schedule' => $schedule,
            'tour' => $tour,
            'adults' => $request->adults,
            'children' => $request->children,
            'totalPersons' => $totalPersons,
            'totalPrice' => $totalPrice,
            'user' => $user,
            'identity' => $identity,
            'holidaySurcharge' => $holidaySurcharge,
            'basePrice' => $basePrice,
            'childPrice' => $childPrice,
            'holidays' => $holidays,
            'coupons' => $coupons,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function payWithVNPay(int $id, Request $request): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($booking->tour_status, [Booking::TOUR_COMPLETED, Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])) {
            return redirect()->route('user.bookings')->with('error', 'Đơn hàng không thể thanh toán.');
        }

        $vnpayUrl = $this->vnPayService->generateUrl($booking, $request->ip());

        return redirect()->away($vnpayUrl);
    }

    public function vnpayReturn(Request $request): RedirectResponse
    {
        if (! $this->vnPayService->validateHash($request->all())) {
            return redirect()->route('user.bookings')->with('error', 'Chữ ký thanh toán không hợp lệ.');
        }

        $result = $this->vnPayService->processTransaction($request->all());

        if ($result['success']) {
            $booking = $result['booking'];
            if ($booking && $booking->transport_type === 'flight') {
                $this->flightService->bookFlightForBooking($booking);

                return redirect()->route('user.bookings')->with('success', 'Thanh toán VNPay thành công. Vé máy bay đã được đặt và gửi vào email của bạn.');
            }

            return redirect()->route('user.bookings')->with('success', 'Thanh toán đặt tour qua VNPay thành công!');
        } else {
            return redirect()->route('user.bookings')->with('error', 'Thanh toán không thành công. Mã lỗi: '.$result['responseCode']);
        }
    }

    public function vnpayIpn(Request $request): JsonResponse
    {
        if (! $this->vnPayService->validateHash($request->all())) {
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid signature',
            ]);
        }

        $txnRef = $request->vnp_TxnRef;
        $parts = explode('_', $txnRef);
        $bookingId = $parts[0] ?? null;

        $booking = Booking::find($bookingId);
        $payment = Payment::where('transaction_code', $txnRef)->first();

        if (! $booking || ! $payment) {
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Order not found',
            ]);
        }

        $vnpAmount = $request->vnp_Amount / 100;
        if ($vnpAmount != $payment->amount) {
            return response()->json([
                'RspCode' => '04',
                'Message' => 'Invalid amount',
            ]);
        }

        if ($payment->payment_status !== 'pending') {
            return response()->json([
                'RspCode' => '02',
                'Message' => 'Order already confirmed',
            ]);
        }

        $this->vnPayService->processTransaction($request->all());

        return response()->json([
            'RspCode' => '00',
            'Message' => 'Confirm Success',
        ]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'order_value' => 'required|numeric|min:0',
            'schedule_id' => 'required|exists:tour_schedules,id',
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->where(function ($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.',
            ], 404);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng.',
            ], 400);
        }

        $schedule = TourSchedule::with('tour.categories')->find($request->schedule_id);
        $tourCategoryIds = $schedule->tour->categories->pluck('id')->toArray();

        if ($coupon->category_id !== null && ! in_array($coupon->category_id, $tourCategoryIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không áp dụng cho loại tour này.',
            ], 400);
        }

        if ($request->order_value < $coupon->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu '.format_currency($coupon->min_order_value),
            ], 400);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percent') {
            $discount = $request->order_value * ($coupon->discount_value / 100);
            if ($coupon->max_discount) {
                $discount = min($discount, $coupon->max_discount);
            }
        } else {
            $discount = $coupon->discount_value;
        }

        $discount = min($discount, $request->order_value);
        $finalPrice = max(0, $request->order_value - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => $discount,
            'final_price' => $finalPrice,
        ]);
    }
}
