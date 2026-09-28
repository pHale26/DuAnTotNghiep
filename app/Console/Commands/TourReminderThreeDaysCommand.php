<?php

namespace App\Console\Commands;

use App\Mail\TourReminderMail;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TourReminderThreeDaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:reminder-three-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động đóng/ẩn lịch trình và gửi email nhắc nhở khách hàng trước 3 ngày khởi hành';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();
        $threeDaysLater = Carbon::today()->addDays(3);

        // Lấy danh sách các lịch trình tour khởi hành trong vòng 3 ngày tới mà chưa được gửi mail nhắc nhở
        $schedules = TourSchedule::whereDate('departure_date', '<=', $threeDaysLater)
            ->whereDate('departure_date', '>=', $today)
            ->where('reminder_sent', false)
            ->with(['tour', 'bookings.user'])
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('Không có lịch trình nào cần xử lý hôm nay.');

            return self::SUCCESS;
        }

        $processedSchedulesCount = 0;
        $totalEmailsSent = 0;

        foreach ($schedules as $schedule) {
            // 1. Tự động ẩn tour bằng cách cập nhật status lịch trình thành 'closed' (nếu đang 'available')
            $originalStatus = $schedule->status;
            if ($schedule->status === 'available') {
                $schedule->status = 'closed';
            }

            // Đánh dấu lịch trình đã gửi nhắc nhở
            $schedule->reminder_sent = true;
            $schedule->save();

            $processedSchedulesCount++;

            // 2. Lấy danh sách booking của lịch trình này mà không bị hủy (booking_status != 'cancelled')
            $bookings = $schedule->bookings()
                ->where('booking_status', '!=', 'cancelled')
                ->get();

            $emailsSentForSchedule = 0;

            foreach ($bookings as $booking) {
                if ($booking->user && $booking->user->email) {
                    try {
                        Mail::to($booking->user->email)->send(new TourReminderMail($booking));
                        $emailsSentForSchedule++;
                        $totalEmailsSent++;
                    } catch (\Exception $e) {
                        $errorMsg = "Lỗi gửi mail nhắc nhở cho booking #{$booking->id} (User ID {$booking->user_id}): ".$e->getMessage();
                        $this->error($errorMsg);
                        Log::error('[tours:reminder-three-days] '.$errorMsg);
                    }
                }
            }

            $infoMsg = "Lịch trình ID {$schedule->id} (Khởi hành: ".$schedule->departure_date->format('d/m/Y').") - Trạng thái ban đầu: '{$originalStatus}' -> Mới: '{$schedule->status}'. Đã gửi {$emailsSentForSchedule} email nhắc nhở.";
            $this->info($infoMsg);
            Log::info('[tours:reminder-three-days] '.$infoMsg);
        }

        $summaryMsg = "Hoàn thành xử lý {$processedSchedulesCount} lịch trình, gửi tổng cộng {$totalEmailsSent} email nhắc nhở.";
        $this->info($summaryMsg);
        Log::info('[tours:reminder-three-days] '.$summaryMsg);

        return self::SUCCESS;
    }
}
