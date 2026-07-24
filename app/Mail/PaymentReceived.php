<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmed - Reference: '.$this->booking->reference,
            to: [$this->booking->participant_email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-received',
            with: [
                'booking' => $this->booking,
            ],
        );
    }
}
