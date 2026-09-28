<?php

namespace App\Notifications\Guide;

use App\Models\GroupSplit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GroupSplitOverdueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public GroupSplit $groupSplit) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $guestName = $this->groupSplit->guest_name;
        $tourCode = $this->groupSplit->guest->booking->tour_schedule->code ?? '';
        $scheduleId = $this->groupSplit->guest->booking->tour_schedule_id ?? 0;

        return [
            'type' => 'group_split_overdue',
            'title' => 'Khách hàng quá giờ tách đoàn',
            'message' => "Hành khách \"{$guestName}\" trong tour \"{$tourCode}\" đã quá giờ hẹn tách đoàn.",
            'group_split_id' => $this->groupSplit->id,
            'link' => route('guide.schedules.show', $scheduleId),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
