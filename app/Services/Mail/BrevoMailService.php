<?php

namespace App\Services\Mail;

use App\Enums\CancellationReason;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BrevoMailService implements TransactionalMailService
{
    private const BREVO_API_URL = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(
        private ?string $apiKey = null,
        private string $fromEmail = 'no-reply@topgradelondonfc.co.uk',
        private string $fromName = 'TopGrade London FC',
        private string $bookingsFromName = 'TopGrade London FC Bookings',
        private string $bookingsEmail = 'bookings@topgradelondonfc.co.uk',
        private string $replyToEmail = 'info@topgradelondonfc.co.uk',
    ) {
        $this->apiKey = $apiKey ?? config('services.brevo.key');
        $this->fromEmail = config('services.brevo.from_email', $this->fromEmail);
        $this->fromName = config('services.brevo.from_name', $this->fromName);
        $this->bookingsFromName = config('services.brevo.bookings_from_name', $this->bookingsFromName);
        $this->bookingsEmail = config('services.brevo.bookings_email', $this->bookingsEmail);
        $this->replyToEmail = config('services.brevo.reply_to_email', $this->replyToEmail);
    }

    public function sendBookingConfirmation(Booking $booking): bool
    {
        $subject = "Your Booking Is Confirmed - Reference: {$booking->reference}";
        $html = view('mail.booking-confirmed', ['booking' => $booking])->render();

        return $this->send(
            type: 'booking_confirmation',
            booking: $booking,
            senderName: $this->fromName,
            senderEmail: $this->fromEmail,
            recipientName: $booking->participant_name,
            recipientEmail: $booking->participant_email,
            replyToName: $this->fromName,
            replyToEmail: $this->replyToEmail,
            subject: $subject,
            htmlContent: $html
        );
    }

    public function sendPaymentReceived(Booking $booking): bool
    {
        $subject = "Payment Confirmed - Reference: {$booking->reference}";
        $html = view('mail.payment-received', ['booking' => $booking])->render();

        return $this->send(
            type: 'payment_received',
            booking: $booking,
            senderName: $this->fromName,
            senderEmail: $this->fromEmail,
            recipientName: $booking->participant_name,
            recipientEmail: $booking->participant_email,
            replyToName: $this->fromName,
            replyToEmail: $this->replyToEmail,
            subject: $subject,
            htmlContent: $html
        );
    }

    public function sendBookingNotification(Booking $booking): bool
    {
        $subject = "New Booking Received - {$booking->reference} ({$booking->participant_name})";
        $html = view('mail.new-booking-admin', ['booking' => $booking])->render();

        return $this->send(
            type: 'booking_admin_notification',
            booking: $booking,
            senderName: $this->bookingsFromName,
            senderEmail: $this->fromEmail,
            recipientName: $this->bookingsFromName,
            recipientEmail: $this->bookingsEmail,
            replyToName: $booking->participant_name,
            replyToEmail: $booking->participant_email,
            subject: $subject,
            htmlContent: $html
        );
    }

    public function sendBookingCancellation(Booking $booking, CancellationReason $reason): bool
    {
        $subject = "Booking Cancelled - Reference: {$booking->reference}";
        $html = view('mail.booking-cancelled', [
            'booking' => $booking,
            'reason' => $reason->customerExplanation(),
        ])->render();

        return $this->send(
            type: 'booking_cancellation',
            booking: $booking,
            senderName: $this->fromName,
            senderEmail: $this->fromEmail,
            recipientName: $booking->participant_name,
            recipientEmail: $booking->participant_email,
            replyToName: $this->fromName,
            replyToEmail: $this->replyToEmail,
            subject: $subject,
            htmlContent: $html
        );
    }

    public function sendPaymentFailed(Booking $booking, ?string $reason = null): bool
    {
        $subject = "Payment Authorization Failed - Reference: {$booking->reference}";
        $html = view('mail.payment-failed', [
            'booking' => $booking,
            'reason' => $reason ?? CancellationReason::PaymentFailed->customerExplanation(),
        ])->render();

        return $this->send(
            type: 'payment_failed',
            booking: $booking,
            senderName: $this->fromName,
            senderEmail: $this->fromEmail,
            recipientName: $booking->participant_name,
            recipientEmail: $booking->participant_email,
            replyToName: $this->fromName,
            replyToEmail: $this->replyToEmail,
            subject: $subject,
            htmlContent: $html
        );
    }

    /**
     * Dispatch email payload to Brevo Transactional REST API.
     */
    private function send(
        string $type,
        Booking $booking,
        string $senderName,
        string $senderEmail,
        string $recipientName,
        string $recipientEmail,
        string $replyToName,
        string $replyToEmail,
        string $subject,
        string $htmlContent
    ): bool {
        // Strict guard: In production, missing Brevo key is a fatal operational error
        if (app()->environment('production') && empty($this->apiKey)) {
            Log::critical('Missing BREVO_API_KEY in production environment.');
            throw new RuntimeException('BREVO_API_KEY is not configured for production transactional email.');
        }

        // When no API key in local/testing environment, log simulation without network call
        if (empty($this->apiKey) && ! app()->environment('production')) {
            Log::info("Simulating Brevo transactional email dispatch [{$type}]", [
                'booking_reference' => $booking->reference,
                'recipient' => $recipientEmail,
                'subject' => $subject,
            ]);

            return true;
        }

        $payload = [
            'sender' => [
                'name' => $senderName,
                'email' => $senderEmail,
            ],
            'to' => [
                [
                    'name' => $recipientName,
                    'email' => $recipientEmail,
                ],
            ],
            'replyTo' => [
                'name' => $replyToName,
                'email' => $replyToEmail,
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'tags' => ['topgrade', $type],
        ];

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post(self::BREVO_API_URL, $payload);

            if ($response->successful()) {
                $messageId = $response->json('messageId') ?? 'unknown';

                Log::info("Brevo transactional email delivered [{$type}]", [
                    'booking_reference' => $booking->reference,
                    'recipient' => $recipientEmail,
                    'brevo_message_id' => $messageId,
                ]);

                return true;
            }

            Log::error("Brevo API responded with error [{$type}]", [
                'booking_reference' => $booking->reference,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error("Brevo API request exception [{$type}]", [
                'booking_reference' => $booking->reference,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
