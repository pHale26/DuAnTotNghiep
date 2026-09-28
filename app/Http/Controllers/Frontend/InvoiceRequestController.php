<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\AdminBookingNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class InvoiceRequestController extends Controller
{
    /**
     * Khách hàng gửi yêu cầu xuất hóa đơn.
     */
    public function store(
        Request $request,
        Booking $booking
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Chỉ chủ đơn đặt tour mới được yêu cầu hóa đơn
        |--------------------------------------------------------------------------
        */
        abort_unless(
            (int) $booking->user_id === (int) Auth::id(),
            403,
            'Bạn không có quyền yêu cầu hóa đơn cho đơn này.'
        );

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra dữ liệu email
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'invoice_email' => [
                'required',
                'email',
                'max:255',
            ],
        ], [
            'invoice_email.required' => 'Vui lòng nhập email nhận hóa đơn.',
            'invoice_email.email' => 'Email nhận hóa đơn không hợp lệ.',
            'invoice_email.max' => 'Email không được vượt quá 255 ký tự.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Chỉ cho yêu cầu khi đã thanh toán
        |--------------------------------------------------------------------------
        */
        if (! in_array(
            $booking->payment_status,
            ['paid_30', 'paid_100'],
            true
        )) {
            return back()->with(
                'error',
                'Bạn chỉ có thể yêu cầu hóa đơn sau khi đã thanh toán.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Không xử lý đơn đã bị hủy
        |--------------------------------------------------------------------------
        */
        if (in_array(
            $booking->tour_status,
            [
                'cancelled_by_customer',
                'cancelled_by_admin',
            ],
            true
        )) {
            return back()->with(
                'error',
                'Đơn đã hủy nên không thể yêu cầu xuất hóa đơn.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Hóa đơn đã gửi rồi
        |--------------------------------------------------------------------------
        */
        if ($booking->invoice_status === 'sent') {
            return back()->with(
                'info',
                'Hóa đơn của đơn này đã được gửi trước đó.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Yêu cầu đang chờ admin xử lý
        |--------------------------------------------------------------------------
        */
        if ($booking->invoice_status === 'requested') {
            return back()->with(
                'info',
                'Yêu cầu xuất hóa đơn đang chờ quản trị viên xử lý.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lưu yêu cầu
        |--------------------------------------------------------------------------
        | Gán trực tiếp để không phụ thuộc vào $fillable.
        */
        $booking->invoice_status = 'requested';
        $booking->invoice_email = $validated['invoice_email'];
        $booking->invoice_requested_at = now();
        $booking->invoice_sent_at = null;
        $booking->save();

        // Bắn thông báo cho Admin
        $admins = User::role('Admin')->get();
        if ($admins->count() > 0) {
            Notification::send($admins, new AdminBookingNotification(
                $booking,
                'invoice_requested',
                'Khách hàng yêu cầu xuất hóa đơn cho đơn hàng: '.$booking->code
            ));
        }

        return redirect()->back()->with('success', 'Đã gửi yêu cầu xuất hóa đơn thành công. Chúng tôi sẽ xử lý và gửi vào email của bạn.');
    }
}
