<?php

namespace App\Notifications\User;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification
{
    use Queueable;

    public $booking;

    public $amount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, $amount)
    {
        $this->booking = $booking;
        $this->amount = $amount;
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
            'type' => 'payment_success',
            'title' => 'Thanh toán thành công',
            'message' => 'Bạn đã thanh toán thành công '.number_format($this->amount).' ₫ cho tour '.$this->booking->tour_schedule->tour->name,
            'booking_id' => $this->booking->id,
            'amount' => $this->amount,
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
