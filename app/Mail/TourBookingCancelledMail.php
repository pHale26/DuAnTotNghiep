<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourBookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $rebookUrl;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->loadMissing([
            'user',
            'tour_schedule.tour',
            'booking_passengers',
        ]);

        $this->rebookUrl = route('frontend.tours.checkout', [
            'schedule_id' => $booking->tour_schedule_id,
            'adults' => $booking->adults_count,
            'children' => $booking->children_count,
        ]);
    }

    public function envelope(): Envelope
    {
        $code = str_pad((string) $this->booking->id, 6, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "Thông báo tự động hủy đơn đặt tour #{$code} do quá hạn 15 phút",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tour_booking_cancelled',
        );
    }
}
