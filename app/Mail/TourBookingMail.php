<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $schedule;

    public $customerName;

    public $customerPhone;
    
    public $linkedUser;

    public function __construct($booking, $schedule = null, $customerName = null, $customerPhone = null, $linkedUser = false)
    {
        // Eager load tất cả các quan hệ cần thiết để render email
        $this->booking = $booking->loadMissing([
            'booking_passengers',
            'addons',
            'user',
            'tour_schedule.tour',
        ]);

        $schedule = $schedule ?? $this->booking->tour_schedule;

        $this->schedule = $schedule ? $schedule->loadMissing([
            'tour.tour_itineraries',
            'tour.departure_location',
            'schedule_guides.tour_guide',
        ]) : null;

        $this->customerName = $customerName ?? ($this->booking->customer_name ?? ($this->booking->user->name ?? ($this->booking->booking_passengers->first()?->full_name ?? 'Quý khách')));
        $this->customerPhone = $customerPhone ?? ($this->booking->customer_phone ?? ($this->booking->user->phone ?? '—'));
        $this->linkedUser = $linkedUser;
    }

    public function envelope(): Envelope
    {
        $code = str_pad((string) $this->booking->id, 6, '0', STR_PAD_LEFT);

        if ($this->booking->payment_status === 'paid_30') {
            $subject = "Xác nhận cọc 30% tour thành công - Mã đơn #{$code}";
        } elseif (in_array($this->booking->payment_status, ['paid_100', 'paid'])) {
            $subject = "Xác nhận thanh toán 100% tour thành công - Mã đơn #{$code}";
        } else {
            $subject = "Xác nhận đặt tour thành công - Mã đơn #{$code}";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tour_booking',
        );
    }
}
