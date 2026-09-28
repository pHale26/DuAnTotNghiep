<?php

namespace App\Notifications\User;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TourDepartureReminderNotification extends Notification
{
    use Queueable;

    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
        $tourName = $this->booking->tour_schedule->tour->name ?? 'Tour của bạn';

        return [
            'type' => 'departure_reminder',
            'title' => 'Nhắc nhở khởi hành',
            'message' => 'Tour "'.$tourName.'" sẽ khởi hành vào ngày mai. Vui lòng chuẩn bị và có mặt đúng giờ nhé!',
            'booking_id' => $this->booking->id,
            'link' => route('user.bookings.detail', $this->booking->id),
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
