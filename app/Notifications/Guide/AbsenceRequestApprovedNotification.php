<?php

namespace App\Notifications\Guide;

use App\Models\TourAbsenceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AbsenceRequestApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $role  'old_main', 'new_main', 'new_backup'
     */
    public function __construct(
        public TourAbsenceRequest $absenceRequest,
        public string $role
    ) {
        // Properties promoted
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
        $departureTime = $this->absenceRequest->tour_schedule?->departure_date ? $this->absenceRequest->tour_schedule->departure_date->format('H:i d/m/Y') : '';

        $title = 'Cập nhật phân công Tour';
        $message = '';
        $link = route('guide.schedules.index');

        if ($this->role === 'old_main') {
            $title = 'Yêu cầu báo bận được duyệt';
            $message = "Yêu cầu báo bận cho tour \"{$tourTitle}\" của bạn đã được duyệt. Bạn đã được gỡ khỏi tour này.";
        } elseif ($this->role === 'new_main') {
            $title = 'Phân công HDV chính mới';
            $message = "Bạn đã được phân công làm HDV chính thay thế cho tour \"{$tourTitle}\" khởi hành lúc {$departureTime}.";
            $link = route('guide.schedules.show', $this->absenceRequest->tour_schedule_id);
        } elseif ($this->role === 'new_backup') {
            $title = 'Phân công HDV phụ mới';
            $message = "Bạn đã được phân công làm HDV phụ thay thế cho tour \"{$tourTitle}\" khởi hành lúc {$departureTime}.";
            $link = route('guide.schedules.show', $this->absenceRequest->tour_schedule_id);
        }

        return [
            'type' => 'absence_approved',
            'title' => $title,
            'message' => $message,
            'absence_request_id' => $this->absenceRequest->id,
            'link' => $link,
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
