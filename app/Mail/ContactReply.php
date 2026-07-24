<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public Inquiry $inquiry,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: '.$this->inquiry->subject,
            to: [$this->inquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-reply',
            with: [
                'contact' => $this->contact,
                'inquiry' => $this->inquiry,
            ],
        );
    }
}
