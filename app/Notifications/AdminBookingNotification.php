<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminBookingNotification extends Notification
{
    use Queueable;

    public $booking;

    public $type;

    public $message;

    public $title;

    /**
     * Create a new notification instance.
     *
     * @param  string  $type  Action type: booking_created, payment_success, booking_cancelled, invoice_requested
     */
    public function __construct(?Booking $booking, string $type, string $message, ?string $title = null)
    {
        $this->booking = $booking;
        $this->type = $type;
        $this->message = $message;

        if (! $title) {
            $titles = [
                'booking_created' => 'Tour mới được đặt',
                'payment_success' => 'Thanh toán thành công',
                'booking_cancelled' => 'Yêu cầu hủy tour',
                'invoice_requested' => 'Yêu cầu xuất hóa đơn',
            ];
            $this->title = $titles[$type] ?? 'Thông báo hệ thống';
        } else {
            $this->title = $title;
        }
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
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'booking_id' => $this->booking ? $this->booking->id : null,
            'link' => $this->booking ? route('admin.bookings.index', ['search' => $this->booking->code]) : route('admin.bookings.index'),
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
