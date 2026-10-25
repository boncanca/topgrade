<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SystemErrorAlertNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $errorClass;

    public string $errorMessage;

    public string $file;

    public int $line;

    public string $url;

    public string $method;

    public string $ip;

    public string $userEmail;

    public string $environment;

    public string $timestamp;

    public string $stackTrace;

    public function __construct(Throwable $exception)
    {
        $this->errorClass = class_basename($exception);
        $this->errorMessage = $exception->getMessage() ?: 'No message provided';
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->url = request()?->fullUrl() ?? 'CLI / Background Process';
        $this->method = request()?->method() ?? 'CLI';
        $this->ip = request()?->ip() ?? 'N/A';
        $this->userEmail = auth()->user()?->email ?? 'Unauthenticated / Guest';
        $this->environment = app()->environment();
        $this->timestamp = now()->format('d M Y, H:i:s T');
        $this->stackTrace = implode("\n", array_slice(explode("\n", $exception->getTraceAsString()), 0, 30));
    }

    public function envelope(): Envelope
    {
        $recipient = env('DEV_SUPPORT_EMAIL', 'support@trupabranding.com');

        return new Envelope(
            from: new Address(
                config('topgrade.emails.no_reply', 'no-reply@topgradelondonfc.co.uk'),
                'TopGrade London FC System Alerts'
            ),
            to: [$recipient],
            subject: "[System Error] {$this->errorClass}: {$this->errorMessage} ({$this->environment})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.system-error-alert',
        );
    }
}
