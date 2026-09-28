<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $schedule;

    public $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        // Eager load necessary relationships
        $this->booking = $booking->loadMissing([
            'user',
            'tour_schedule.tour',
            'booking_passengers',
            'addons',
        ]);

        $this->schedule = $booking->tour_schedule;
        $this->customerName = $booking->user->name ?? 'Quý khách';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tourTitle = $this->schedule->tour->getTranslation('title', 'vi') ?? 'Tour';

        return new Envelope(
            subject: 'Nhắc nhở khởi hành: '.$tourTitle.' - Mã đơn #'.str_pad($this->booking->id, 6, '0', STR_PAD_LEFT),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tour_reminder',
        );
    }
}
