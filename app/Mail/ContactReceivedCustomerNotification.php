<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReceivedCustomerNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('topgrade.emails.info', 'info@topgradelondonfc.co.uk'),
                config('mail.from.name', 'TopGrade London FC')
            ),
            replyTo: [
                new Address(
                    config('topgrade.emails.info', 'info@topgradelondonfc.co.uk'),
                    config('mail.from.name', 'TopGrade London FC')
                ),
            ],
            subject: 'Thank You for Contacting TopGrade London FC',
            to: [$this->inquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-received-customer',
            with: [
                'inquiry' => $this->inquiry,
            ],
        );
    }
}
