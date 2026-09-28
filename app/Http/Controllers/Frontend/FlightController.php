<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\FlightTicketMail;
use App\Models\Booking;
use App\Models\BookingPassenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class FlightController extends Controller
{
    public function search(Request $request)
    {
        $origin = $request->input('origin', 'HAN');
        $destination = $request->input('destination', 'SGN');
        $departureDate = $request->input('departure_date', date('Y-m-d', strtotime('+7 days')));
        $passengersCount = (int) $request->input('passengers', 1);
        $cabinClass = $request->input('cabin_class', 'economy');

        $passengers = [];
        for ($i = 0; $i < $passengersCount; $i++) {
            $passengers[] = ['type' => 'adult'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('DUFFEL_ACCESS_TOKEN'),
            'Duffel-Version' => 'v2',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.duffel.com/air/offer_requests', [
            'data' => [
                'slices' => [
                    [
                        'origin' => $origin,
                        'destination' => $destination,
                        'departure_date' => $departureDate,
                    ],
                ],
                'passengers' => $passengers,
                'cabin_class' => $cabinClass,
            ],
        ]);

        if (! $response->successful()) {
            // dd('Chi tiết lỗi từ Duffel API:', $response->json());
        }

        $offers = $response->json()['data']['offers'] ?? [];

        return view('frontend.flights.index', compact('offers'));
    }

    public function searchApi(Request $request)
    {
        $origin = $request->input('origin', 'HAN');
        $destination = $request->input('destination', 'SGN');
        $departureDate = $request->input('departure_date', date('Y-m-d', strtotime('+7 days')));

        // Duffel API requires dates to be in the future. If test data has past dates, shift it to future.
        if (strtotime($departureDate) < strtotime(date('Y-m-d'))) {
            $departureDate = date('Y-m-d', strtotime('+7 days'));
        }

        $passengersCount = (int) $request->input('passengers', 1);
        $cabinClass = $request->input('cabin_class', 'economy');

        $passengers = [];
        for ($i = 0; $i < $passengersCount; $i++) {
            $passengers[] = ['type' => 'adult'];
        }

        if ($origin === $destination) {
            return response()->json([
                'success' => false,
                'message' => 'Điểm xuất phát trùng với điểm tập trung của Tour. Vui lòng chọn Tự Túc hoặc chọn điểm xuất phát khác.',
            ]);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('DUFFEL_ACCESS_TOKEN'),
            'Duffel-Version' => 'v2',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.duffel.com/air/offer_requests', [
            'data' => [
                'slices' => [
                    [
                        'origin' => $origin,
                        'destination' => $destination,
                        'departure_date' => $departureDate,
                    ],
                ],
                'passengers' => $passengers,
                'cabin_class' => $cabinClass,
            ],
        ]);

        if (! $response->successful()) {
            $errorMsg = 'Lỗi từ Duffel API';
            $errorData = $response->json();
            if (isset($errorData['errors'][0]['message'])) {
                $errorMsg = $errorData['errors'][0]['message'];
            }

            return response()->json(['success' => false, 'message' => $errorMsg]);
        }

        $offers = $response->json()['data']['offers'] ?? [];

        return response()->json(['success' => true, 'data' => $offers]);
    }

    public function checkout(Request $request)
    {
        $offerId = $request->query('offer_id');
        $passengerId = $request->query('passenger_id');
        $tourBookingId = $request->query('tour_booking_id');

        // Nhận giá vé để truyền sang View
        $totalAmount = $request->query('total_amount');
        $totalCurrency = $request->query('total_currency');

        $passenger = null;
        $user = Auth::user();

        if ($tourBookingId) {
            $passenger = BookingPassenger::where('booking_id', $tourBookingId)->first();
        }

        return view('frontend.flights.checkout', compact('offerId', 'passengerId', 'tourBookingId', 'passenger', 'user', 'totalAmount', 'totalCurrency'));
    }

    public function book(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('DUFFEL_ACCESS_TOKEN'),
            'Duffel-Version' => 'v2',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.duffel.com/air/orders', [
            'data' => [
                'type' => 'instant',
                'selected_offers' => [$request->offer_id],
                'passengers' => [
                    [
                        'id' => $request->passenger_id,
                        'title' => $request->title,
                        'family_name' => $request->family_name,
                        'given_name' => $request->given_name,
                        'phone_number' => str_replace(' ', '', $request->phone_number), // Xóa khoảng trắng để tránh lỗi format
                        'email' => $request->email,
                        'born_on' => $request->born_on,
                        'gender' => $request->gender,
                    ],
                ],
                // BẮT BUỘC: Cung cấp thông tin thanh toán cho Duffel
                'payments' => [
                    [
                        'type' => 'balance', // Dùng số dư khả dụng (phù hợp môi trường test)
                        'amount' => strval($request->total_amount),
                        'currency' => $request->total_currency,
                    ],
                ],
            ],
        ]);

        if (! $response->successful()) {
            // Tạm thời hiển thị toàn bộ lỗi từ API ra màn hình để dễ dàng tìm lỗi nếu còn vướng mắc
            dd('Chi tiết lỗi từ hệ thống vé máy bay:', $response->json());
        }

        // lấy mã pnr từ api trả về
        $bookingRef = $response->json()['data']['booking_reference'] ?? 'Chưa cấp mã PNR';

        // tìm đơn đặt tour và cập nhật mã pnr
        $booking = Booking::find($request->tour_booking_id);
        if ($booking) {
            $booking->pnr_code = $bookingRef;
            $booking->save();
        }

        // gửi email cho khách hàng
        $passengerName = $request->family_name.' '.$request->given_name;
        try {
            Mail::to($request->email)->send(
                new FlightTicketMail($booking, $bookingRef, $passengerName)
            );
        } catch (\Exception $e) {
            // bỏ qua lỗi gửi mail để không làm gián đoạn trải nghiệm hiển thị thông báo
        }

        return redirect()->route('home')->with('success', 'Tuyệt vời! Mã chuẩn chi chuyến bay (PNR) của bạn là: '.$bookingRef.'. Vé điện tử đã được gửi vào email của bạn.');
    }
}
