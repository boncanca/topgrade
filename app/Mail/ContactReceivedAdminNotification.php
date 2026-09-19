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

class ContactReceivedAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('topgrade.emails.no_reply', 'no-reply@topgradelondonfc.co.uk'),
                config('mail.from.name', 'TopGrade London FC')
            ),
            replyTo: [
                new Address(
                    $this->inquiry->email,
                    $this->inquiry->name
                ),
            ],
            subject: 'New Contact Enquiry: '.$this->inquiry->subject.' — '.$this->inquiry->name,
            to: [config('topgrade.emails.info', 'info@topgradelondonfc.co.uk')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-received-admin',
            with: [
                'inquiry' => $this->inquiry,
            ],
        );
    }
}
