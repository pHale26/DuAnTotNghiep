<?php

namespace App\Http\Controllers\Frontend;

use App\Exports\PassengerTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PassengersImport;
use App\Models\Booking;
use App\Models\BookingPassenger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BookingPassengerController extends Controller
{
    public function index($id)
    {
        $booking = Booking::with('booking_passengers', 'tour_schedule.tour')->findOrFail($id);

        // Chỉ cho phép khách hàng sửa danh sách khi chưa gửi và cách giờ khởi hành >= 24h
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $departureTime = Carbon::parse($booking->tour_schedule->departure_date);
        $isLocked = now()->diffInHours($departureTime, false) <= 24;

        return view('frontend.bookings.passengers', compact('booking', 'isLocked'));
    }

    public function storeManual(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        if ($booking->is_passenger_list_submitted) {
            return back()->with('error', 'Bạn đã gửi danh sách hành khách.');
        }

        $departureTime = Carbon::parse($booking->tour_schedule->departure_date);
        if (now()->diffInHours($departureTime, false) <= 24) {
            return back()->with('error', 'Hệ thống đã khóa tính năng bổ sung hành khách (do sát ngày khởi hành). Vui lòng liên hệ HDV hoặc tổng đài.');
        }

        $request->validate([
            'passengers' => 'required|array',
            'passengers.*.full_name' => 'required|string|max:255',
            'passengers.*.identity_number' => 'nullable|string|max:50',
            'passengers.*.date_of_birth' => 'nullable|date',
            'passengers.*.gender' => 'nullable|in:male,female,other',
            'passengers.*.passenger_type' => 'required|in:adult,child',
        ]);

        $passengersData = $request->passengers;
        $totalAllowed = $booking->adults_count + $booking->children_count;

        // Bỏ qua khách đầu tiên vì đã là trưởng đoàn và đã lưu lúc checkout,
        // ở form chúng ta sẽ hiển thị readonly cho khách đầu tiên.
        // Hoặc xóa danh sách cũ (ngoại trừ trưởng đoàn) rồi thêm mới.

        // Trưởng đoàn (người đầu tiên của booking)
        $leader = $booking->booking_passengers()->orderBy('id')->first();

        // Xóa các khách còn lại
        if ($leader) {
            $booking->booking_passengers()->where('id', '!=', $leader->id)->delete();
        } else {
            $booking->booking_passengers()->delete();
        }

        foreach ($passengersData as $index => $pData) {
            // Cập nhật trưởng đoàn nếu index = 0, ngược lại tạo mới
            if ($index == 0 && $leader) {
                $leader->update([
                    'full_name' => $pData['full_name'],
                    'identity_number' => $pData['identity_number'],
                    'date_of_birth' => $pData['date_of_birth'],
                    'gender' => $pData['gender'],
                ]);
            } else {
                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'full_name' => $pData['full_name'],
                    'identity_number' => $pData['identity_number'] ?? null,
                    'date_of_birth' => $pData['date_of_birth'] ?? null,
                    'gender' => $pData['gender'] ?? 'other',
                    'passenger_type' => $pData['passenger_type'],
                ]);
            }
        }

        // Đánh dấu là đã nộp
        $booking->update(['is_passenger_list_submitted' => true]);

        return redirect()->route('user.bookings.detail', $booking->id)->with('success', 'Đã bổ sung danh sách hành khách thành công!');
    }

    public function downloadTemplate()
    {
        return Excel::download(new PassengerTemplateExport, 'Mau_Danh_Sach_Hanh_Khach.xlsx');
    }

    public function importExcel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        if ($booking->is_passenger_list_submitted) {
            return back()->with('error', 'Bạn đã gửi danh sách hành khách.');
        }

        $departureTime = Carbon::parse($booking->tour_schedule->departure_date);
        if (now()->diffInHours($departureTime, false) <= 24) {
            return back()->with('error', 'Hệ thống đã khóa tính năng bổ sung hành khách.');
        }

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $import = new PassengersImport($booking);
            Excel::import($import, $request->file('excel_file'));

            $booking->update(['is_passenger_list_submitted' => true]);

            return redirect()->route('user.bookings.detail', $booking->id)->with('success', 'Đã import danh sách hành khách thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi import: '.$e->getMessage());
        }
    }
}
