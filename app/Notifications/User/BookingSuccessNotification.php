<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingSuccessNotification extends Notification
{
    use Queueable;

    public $bookingId;

    public $tourName;

    /**
     * Create a new notification instance.
     */
    public function __construct($bookingId, $tourName)
    {
        $this->bookingId = $bookingId;
        $this->tourName = $tourName;
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
            'booking_id' => $this->bookingId,
            'tour_name' => $this->tourName,
            'message' => 'Đặt tour "'.$this->tourName.'" thành công!',
            'type' => 'booking_success',
        ];
    }
}
