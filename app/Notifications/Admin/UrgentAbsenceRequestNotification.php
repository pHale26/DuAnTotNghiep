<?php

namespace App\Notifications\Admin;

use App\Models\TourAbsenceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UrgentAbsenceRequestNotification extends Notification
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
        $guideName = $this->absenceRequest->main_guide?->name ?? 'Hướng dẫn viên';
        $tourTitle = $this->absenceRequest->tour?->title ?? '';
        $departureTime = $this->absenceRequest->tour_schedule?->departure_date ? $this->absenceRequest->tour_schedule->departure_date->format('H:i d/m/Y') : '';

        return [
            'type' => 'booking_cancelled', // using booking_cancelled type to get a red background alert icon
            'title' => 'Yêu cầu báo bận KHẨN CẤP',
            'message' => "HDV {$guideName} báo bận cho tour \"{$tourTitle}\" khởi hành lúc {$departureTime}.",
            'absence_request_id' => $this->absenceRequest->id,
            'link' => route('admin.reports.index').'?tab=absence',
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
