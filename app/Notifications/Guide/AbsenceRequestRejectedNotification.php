<?php

namespace App\Notifications\Guide;

use App\Models\TourAbsenceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AbsenceRequestRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TourAbsenceRequest $absenceRequest)
    {
        // Property promoted
    }

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
        $tourTitle = $this->absenceRequest->tour?->title ?? '';
        $rejectReason = $this->absenceRequest->reject_reason ?? 'Không có lý do chi tiết.';

        return [
            'type' => 'absence_rejected',
            'title' => 'Yêu cầu báo bận bị từ chối',
            'message' => "Yêu cầu báo bận cho tour \"{$tourTitle}\" đã bị từ chối. Lý do: {$rejectReason}",
            'absence_request_id' => $this->absenceRequest->id,
            'link' => route('guide.schedules.show', $this->absenceRequest->tour_schedule_id),
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
