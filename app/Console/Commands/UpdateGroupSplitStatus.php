<?php

namespace App\Console\Commands;

use App\Models\GroupSplit;
use App\Models\GroupSplitLog;
use App\Notifications\Guide\GroupSplitOverdueNotification;
use App\Notifications\Guide\GroupSplitUnreachableNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateGroupSplitStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group-splits:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto update group split status to OVERDUE or UNREACHABLE based on time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Rule 2: UNREACHABLE priority (if > 60 minutes past split_started_at)
        // Check for ON_TIME or OVERDUE splits
        $unreachableSplits = GroupSplit::whereIn('status', [GroupSplit::STATUS_ON_TIME, GroupSplit::STATUS_OVERDUE])
            ->where('split_started_at', '<', $now->copy()->subMinutes(60))
            ->with(['guest.booking.tour_schedule.schedule_guides.tour_guide.user'])
            ->get();

        foreach ($unreachableSplits as $split) {
            $oldStatus = $split->status;
            $split->update(['status' => GroupSplit::STATUS_UNREACHABLE]);

            GroupSplitLog::create([
                'group_split_id' => $split->id,
                'old_status' => $oldStatus,
                'new_status' => GroupSplit::STATUS_UNREACHABLE,
                'description' => 'Hệ thống tự động đổi trạng thái sang UNREACHABLE do đã quá 60 phút kể từ lúc tách đoàn.',
                'triggered_by' => null, // null means system/cron
            ]);

            // Notify guides
            $schedule = $split->guest?->booking?->tour_schedule;
            if ($schedule) {
                foreach ($schedule->schedule_guides as $sg) {
                    $guideUser = $sg->tour_guide?->user;
                    if ($guideUser) {
                        $guideUser->notify(new GroupSplitUnreachableNotification($split));
                    }
                }
            }

            $this->info("Split ID {$split->id} marked as UNREACHABLE.");
        }

        // Rule 1: OVERDUE (if past end_time)
        // Check ONLY ON_TIME splits
        $overdueSplits = GroupSplit::where('status', GroupSplit::STATUS_ON_TIME)
            ->where('end_time', '<', $now)
            ->with(['guest.booking.tour_schedule.schedule_guides.tour_guide.user'])
            ->get();

        foreach ($overdueSplits as $split) {
            $oldStatus = $split->status;
            $split->update(['status' => GroupSplit::STATUS_OVERDUE]);

            GroupSplitLog::create([
                'group_split_id' => $split->id,
                'old_status' => $oldStatus,
                'new_status' => GroupSplit::STATUS_OVERDUE,
                'description' => 'Hệ thống tự động đổi trạng thái sang OVERDUE do đã quá giờ hẹn.',
                'triggered_by' => null, // null means system/cron
            ]);

            // Notify guides
            $schedule = $split->guest?->booking?->tour_schedule;
            if ($schedule) {
                foreach ($schedule->schedule_guides as $sg) {
                    $guideUser = $sg->tour_guide?->user;
                    if ($guideUser) {
                        $guideUser->notify(new GroupSplitOverdueNotification($split));
                    }
                }
            }

            $this->info("Split ID {$split->id} marked as OVERDUE.");
        }

        $this->info('Group split statuses updated successfully.');
    }
}
