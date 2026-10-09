<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $isPendingPayment = (float) $this->booking->amount > 0 && ($this->booking->payment_status?->value ?? (string) $this->booking->payment_status) !== 'paid';
        $subject = $isPendingPayment
            ? 'Booking Reserved · Awaiting Bank Transfer - Reference: '.$this->booking->reference
            : 'Your Booking Is Confirmed - Reference: '.$this->booking->reference;

        return new Envelope(
            from: new Address(
                config('topgrade.emails.no_reply', 'no-reply@topgradelondonfc.co.uk'),
                config('mail.from.name', 'TopGrade London FC')
            ),
            replyTo: [
                new Address(
                    config('topgrade.emails.info', 'info@topgradelondonfc.co.uk'),
                    config('mail.from.name', 'TopGrade London FC')
                ),
            ],
            subject: $subject,
            to: [$this->booking->participant_email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking-confirmed',
            with: [
                'booking' => $this->booking,
                'bank' => config('topgrade.bank'),
            ],
        );
    }
}
