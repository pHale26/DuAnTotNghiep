<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\TicketBookingRequest;
use App\Http\Requests\Frontend\TicketCheckoutRequest;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketBooking;
use App\Models\TicketOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketBookingController extends Controller
{
    /**
     * Show checkout page for tickets
     */
    public function checkout(TicketCheckoutRequest $request)
    {
        $ticket = Ticket::with(['destination', 'ticket_images', 'ticket_options'])
            ->findOrFail($request->ticket_id);

        $ticketOption = TicketOption::findOrFail($request->ticket_option_id);

        if ($ticketOption->ticket_id !== $ticket->id) {
            return redirect()->back()
                ->with('error', 'Loại vé không hợp lệ.')
                ->withInput();
        }

        $quantity = $request->quantity;
        $visitDate = $request->visit_date;
        $subtotal = $ticketOption->price * $quantity;

        return view('frontend.tickets.checkout', compact(
            'ticket',
            'ticketOption',
            'quantity',
            'visitDate',
            'subtotal'
        ));
    }

    /**
     * Store ticket booking
     */
    public function store(TicketBookingRequest $request)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $ticketOption = TicketOption::with('ticket')->findOrFail($request->ticket_option_id);
            $quantity = $request->quantity;
            $subtotal = $ticketOption->price * $quantity;
            $discountAmount = 0;
            $couponId = null;

            // Apply coupon if provided
            if ($request->filled('coupon_code')) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where(function ($query) {
                        $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                    })
                    ->where(function ($query) {
                        $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                    })
                    ->first();

                if ($coupon && $subtotal >= $coupon->min_order_value) {
                    if ($coupon->usage_limit === null || $coupon->used_count < $coupon->usage_limit) {
                        $discount = 0;
                        if ($coupon->discount_type === 'percent') {
                            $discount = $subtotal * ($coupon->discount_value / 100);
                            if ($coupon->max_discount) {
                                $discount = min($discount, $coupon->max_discount);
                            }
                        } else {
                            $discount = $coupon->discount_value;
                        }
                        $discountAmount = $discount;
                        $couponId = $coupon->id;

                        $coupon->increment('used_count');
                    }
                }
            }

            $finalPrice = max(0, $subtotal - $discountAmount);

            // Create ticket booking
            $ticketBooking = new TicketBooking;
            $ticketBooking->user_id = $user->id;
            $ticketBooking->ticket_option_id = $ticketOption->id;
            $ticketBooking->quantity = $quantity;
            $ticketBooking->total_price = $finalPrice;
            $ticketBooking->discount_amount = $discountAmount;
            $ticketBooking->coupon_id = $couponId;
            $ticketBooking->visit_date = $request->visit_date;
            $ticketBooking->booking_status = 'pending';
            $ticketBooking->save();

            // Generate QR Code
            $qrCodeUrl = $this->generateQrCode($ticketBooking);
            $ticketBooking->qr_code_url = $qrCodeUrl;
            $ticketBooking->save();

            DB::commit();

            // Send confirmation email
            try {
                // TODO: Implement email sending
                // Mail::to($request->customer_email)->send(new TicketBookingMail($ticketBooking));
            } catch (\Exception $e) {
                Log::error('Lỗi gửi mail đặt vé: '.$e->getMessage());
            }

            // Handle payment method
            if ($request->payment_method === 'vnpay') {
                $vnpayUrl = $this->generateVnpayUrl($ticketBooking, $request->ip());

                return redirect()->away($vnpayUrl);
            }

            return redirect()->route('frontend.tickets.booking.success', $ticketBooking->id)
                ->with('success', 'Đặt vé thành công! Vui lòng thanh toán để hoàn tất.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi đặt vé: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Đã có lỗi xảy ra. Vui lòng thử lại.')
                ->withInput();
        }
    }

    /**
     * Show booking success page
     */
    public function success($id)
    {
        $booking = TicketBooking::with(['ticket_option.ticket', 'user', 'coupon'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('frontend.tickets.success', compact('booking'));
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'total_price' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
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

        if ($request->total_price < $coupon->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu '.format_currency($coupon->min_order_value),
            ], 400);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percent') {
            $discount = $request->total_price * ($coupon->discount_value / 100);
            if ($coupon->max_discount) {
                $discount = min($discount, $coupon->max_discount);
            }
        } else {
            $discount = $coupon->discount_value;
        }

        $finalPrice = max(0, $request->total_price - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount' => $discount,
            'final_price' => $finalPrice,
            'discount_formatted' => format_currency($discount),
            'final_price_formatted' => format_currency($finalPrice),
        ]);
    }

    /**
     * VNPay return handler
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TxnRef = $request->vnp_TxnRef;

        if ($vnp_ResponseCode == '00') {
            // Parse booking ID from transaction ref
            $bookingId = (int) explode('_', $vnp_TxnRef)[1];
            $booking = TicketBooking::findOrFail($bookingId);

            DB::beginTransaction();
            try {
                // Update booking status
                $booking->booking_status = 'confirmed';
                $booking->save();

                // Create payment record
                $payment = new Payment;
                $payment->ticket_booking_id = $booking->id;
                $payment->payment_method = 'vnpay';
                $payment->amount = $booking->total_price;
                $payment->payment_status = 'completed';
                $payment->payment_date = now();
                $payment->transaction_id = $request->vnp_TransactionNo ?? null;
                $payment->save();

                DB::commit();

                return redirect()->route('frontend.tickets.booking.success', $booking->id)
                    ->with('success', 'Thanh toán thành công! Vé của bạn đã được xác nhận.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Lỗi xử lý VNPay return: '.$e->getMessage());

                return redirect()->route('home')
                    ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán.');
            }
        } else {
            return redirect()->route('home')
                ->with('error', 'Thanh toán không thành công. Vui lòng thử lại.');
        }
    }

    /**
     * Generate VNPay payment URL
     */
    private function generateVnpayUrl(TicketBooking $booking, $ipAddr)
    {
        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_Returnurl = route('frontend.tickets.vnpay_return');

        $vnp_TxnRef = 'TICKET_'.$booking->id.'_'.time();
        $vnp_OrderInfo = 'Thanh toan ve tham quan #'.$booking->id;
        $vnp_Amount = $booking->total_price * 100;
        $vnp_Locale = app()->getLocale() === 'vi' ? 'vn' : 'en';
        $vnp_BankCode = '';
        $vnp_IpAddr = $ipAddr;

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        if (strlen($vnp_BankCode) > 0) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.$key.'='.$value;
            } else {
                $hashdata .= $key.'='.$value;
                $i = 1;
            }
            $query .= urlencode($key).'='.urlencode($value).'&';
        }

        $vnp_Url = $vnp_Url.'?'.$query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash='.$vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Generate QR Code for ticket
     */
    private function generateQrCode(TicketBooking $booking): string
    {
        $qrData = urlencode(json_encode([
            'booking_id' => $booking->id,
            'ticket_option_id' => $booking->ticket_option_id,
            'quantity' => $booking->quantity,
            'visit_date' => $booking->visit_date->format('Y-m-d'),
            'user_id' => $booking->user_id,
        ]));

        // Use Google Charts API for QR Code generation
        $qrCodeUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={$qrData}&choe=UTF-8";

        // Download and save QR code
        try {
            $qrCodeImage = file_get_contents($qrCodeUrl);
            $fileName = 'ticket_'.$booking->id.'_'.time().'.png';
            $path = storage_path('app/public/qrcodes/'.$fileName);

            if (! file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, $qrCodeImage);

            return '/storage/qrcodes/'.$fileName;
        } catch (\Exception $e) {
            Log::error('Lỗi tạo QR code: '.$e->getMessage());

            // Fallback to using the API URL directly
            return $qrCodeUrl;
        }
    }
}
