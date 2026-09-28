<?php

namespace App\Notifications\Guide;

use App\Models\TourReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TourReportRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TourReport $tourReport, public string $reason) {}

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
        $tourTitle = $this->tourReport->tour_schedule->tour->title ?? '';
        $scheduleId = $this->tourReport->tour_schedule_id;

        return [
            'type' => 'tour_report_rejected',
            'title' => 'Báo cáo Tour bị từ chối',
            'message' => "Báo cáo cho tour \"{$tourTitle}\" đã bị Admin từ chối. Lý do: {$this->reason}",
            'tour_report_id' => $this->tourReport->id,
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
