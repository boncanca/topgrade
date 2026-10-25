<?php

namespace App\Mail;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryQuickReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry,
        public string $replyMessage,
        public string $replySubject,
        public ?User $sender = null,
    ) {}

    public function envelope(): Envelope
    {
        $clubName = (string) config('app.name', 'Top Grade London FC');

        return new Envelope(
            to: [
                new Address($this->inquiry->email, $this->inquiry->name),
            ],
            subject: $this->replySubject,
            replyTo: [
                new Address(
                    (string) config('mail.from.address', 'support@topgradelondonfc.co.uk'),
                    $clubName
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.inquiry-quick-reply',
            with: [
                'inquiry' => $this->inquiry,
                'replyMessage' => $this->replyMessage,
                'sender' => $this->sender,
            ],
        );
    }
}
