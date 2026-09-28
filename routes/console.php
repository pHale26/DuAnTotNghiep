<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tự động chuyển trạng thái booking sang "Đang thực hiện" khi đến giờ khởi hành
Schedule::command('tours:update-status')->everyMinute();

// Tự động ẩn/đóng tour và gửi mail nhắc nhở khi còn 3 ngày trước khởi hành
Schedule::command('tours:reminder-three-days')->daily();

// Tự động cập nhật vòng đời của Tour (đang chạy, đã kết thúc)
Schedule::command('tours:update-lifecycle')->daily();

// Tự động phân phối lại cuộc trò chuyện nếu nhân viên phụ trách đã offline hoặc ngưng hoạt động
Schedule::command('chat:re-route-offline')->everyMinute();

// Tự động hủy đơn hàng chưa thanh toán quá 30 phút
Schedule::command('bookings:cancel-unpaid')->everyMinute();

// Tự động gửi thông báo nhắc nhở khách hàng trước 1 ngày khởi hành
Schedule::command('tours:send-reminders')->dailyAt('08:00');

// Tự động xóa ảnh CCCD sau 10 ngày kết thúc tour
Schedule::command('identities:cleanup')->hourly();
Schedule::command('group-splits:update-status')->everyMinute();
