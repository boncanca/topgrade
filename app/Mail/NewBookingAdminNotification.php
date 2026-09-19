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

class NewBookingAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('topgrade.emails.no_reply', 'no-reply@topgradelondonfc.co.uk'),
                config('mail.from.name', 'TopGrade London FC')
            ),
            replyTo: [
                new Address(
                    $this->booking->participant_email,
                    $this->booking->participant_name
                ),
            ],
            subject: 'New Booking Received: '.$this->booking->reference.' — '.$this->booking->participant_name,
            to: [config('topgrade.emails.bookings', 'bookings@topgradelondonfc.co.uk')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.new-booking-admin',
            with: [
                'booking' => $this->booking,
            ],
        );
    }
}
