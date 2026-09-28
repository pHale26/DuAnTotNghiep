<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FlightTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $pnrCode;

    public $passengerName;

    public function __construct($booking, $pnrCode, $passengerName)
    {
        $this->booking = $booking;
        $this->pnrCode = $pnrCode;
        $this->passengerName = $passengerName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đặt vé máy bay và tour du lịch',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.flight_ticket',
        );
    }
}
