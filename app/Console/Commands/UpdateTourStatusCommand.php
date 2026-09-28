<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\TourSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateTourStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chuyển trạng thái booking sang "in_progress" khi tour đến ngày khởi hành';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $todayDate = now()->toDateString();

        $scheduleExists = TourSchedule::whereDate('departure_date', '<=', $todayDate)
            ->whereDate('return_date', '>=', $todayDate)
            ->exists();

        if (! $scheduleExists) {
            $this->info('Không có lịch trình nào đang diễn ra hôm nay.');

            return self::SUCCESS;
        }

        $beforeCount = Booking::where('tour_status', Booking::TOUR_UPCOMING)->count();

        Booking::updateUpcomingTourStatuses();

        $afterCount = Booking::where('tour_status', Booking::TOUR_UPCOMING)->count();
        $updatedCount = $beforeCount - $afterCount;

        $message = "Đã cập nhật {$updatedCount} booking sang trạng thái 'in_progress'.";

        $this->info($message);
        Log::info('[tours:update-status] '.$message);

        return self::SUCCESS;
    }
}
