<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    private const BREVO_API_URL = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(
        protected string $apiKey,
    ) {
        parent::__construct();
    }

    /**
     * Send email via Brevo REST API v3.
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $envelope = $message->getEnvelope();

        $sender = $envelope->getSender();
        $senderPayload = array_filter([
            'name' => $sender->getName() ?: null,
            'email' => $sender->getAddress(),
        ]);

        $recipients = array_values(array_map(function (Address $address) {
            return array_filter([
                'name' => $address->getName() ?: null,
                'email' => $address->getAddress(),
            ]);
        }, $this->getRecipients($email, $envelope)));

        $payload = [
            'sender' => $senderPayload,
            'to' => $recipients,
            'subject' => $email->getSubject() ?? '(no subject)',
        ];

        if ($html = $email->getHtmlBody()) {
            $payload['htmlContent'] = is_resource($html) ? stream_get_contents($html) : (string) $html;
        }

        if ($text = $email->getTextBody()) {
            $payload['textContent'] = is_resource($text) ? stream_get_contents($text) : (string) $text;
        }

        if (empty($payload['htmlContent']) && empty($payload['textContent'])) {
            $payload['textContent'] = ' ';
        }

        $replyToList = $email->getReplyTo();
        if (! empty($replyToList)) {
            $replyTo = $replyToList[0];
            $payload['replyTo'] = array_filter([
                'name' => $replyTo->getName() ?: null,
                'email' => $replyTo->getAddress(),
            ]);
        }

        if (! empty($email->getCc())) {
            $payload['cc'] = array_values(array_map(fn (Address $addr) => array_filter([
                'name' => $addr->getName() ?: null,
                'email' => $addr->getAddress(),
            ]), $email->getCc()));
        }

        if (! empty($email->getBcc())) {
            $payload['bcc'] = array_values(array_map(fn (Address $addr) => array_filter([
                'name' => $addr->getName() ?: null,
                'email' => $addr->getAddress(),
            ]), $email->getBcc()));
        }

        if ($email->getAttachments()) {
            $attachments = [];
            foreach ($email->getAttachments() as $attachment) {
                $headers = $attachment->getPreparedHeaders();
                $filename = $headers->getHeaderParameter('Content-Disposition', 'filename') ?? 'attachment';
                $attachments[] = [
                    'name' => $filename,
                    'content' => base64_encode($attachment->getBody()),
                ];
            }
            if (! empty($attachments)) {
                $payload['attachment'] = $attachments;
            }
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post(self::BREVO_API_URL, $payload);
        } catch (\Throwable $e) {
            throw new TransportException('Request to Brevo API failed: '.$e->getMessage(), 0, $e);
        }

        if (! $response->successful()) {
            $errorMsg = $response->json('message') ?? $response->body();
            throw new TransportException("Brevo API responded with status {$response->status()}: {$errorMsg}", $response->status());
        }

        $messageId = $response->json('messageId') ?? 'unknown';
        $email->getHeaders()->addHeader('X-Brevo-Message-ID', (string) $messageId);
    }

    /**
     * Get the recipients excluding CC or BCC.
     *
     * @return Address[]
     */
    protected function getRecipients(Email $email, Envelope $envelope): array
    {
        return array_filter($envelope->getRecipients(), function (Address $address) use ($email) {
            return ! in_array($address, array_merge($email->getCc(), $email->getBcc()), true);
        });
    }

    /**
     * String representation of the transport.
     */
    public function __toString(): string
    {
        return 'brevo';
    }
}
