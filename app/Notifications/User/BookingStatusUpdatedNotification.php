<?php

namespace App\Notifications\User;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BookingStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $booking;

    public $messageStr;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $messageStr)
    {
        $this->booking = $booking;
        $this->messageStr = $messageStr;
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
        return [
            'type' => 'booking_status',
            'title' => 'Cập nhật trạng thái Tour',
            'message' => $this->messageStr,
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
