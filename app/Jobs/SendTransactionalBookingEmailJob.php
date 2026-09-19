<?php

namespace App\Jobs;

use App\Enums\CancellationReason;
use App\Models\Booking;
use App\Services\Mail\TransactionalMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendTransactionalBookingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $bookingId,
        public string $emailType,
        public array $payload = [],
    ) {}

    public function handle(TransactionalMailService $mailService): void
    {
        $canonicalType = match ($this->emailType) {
            'payment_received', 'payment-received' => 'payment-received',
            'confirmation', 'booking_confirmation', 'booking-confirmation' => 'booking-confirmation',
            'cancellation', 'booking_cancellation', 'booking-cancellation' => 'booking-cancellation',
            'payment_failed', 'payment-failed' => 'payment-failed',
            'admin_notification', 'booking_notification', 'booking-notification' => 'booking-notification',
            default => str_replace('_', '-', $this->emailType),
        };

        $idempotencyKey = "booking:{$this->bookingId}:{$canonicalType}";

        // Atomic check: Ensure each logical notification for a booking is dispatched at most once
        if (! Cache::add($idempotencyKey, true, now()->addDays(7))) {
            Log::info("Skipping duplicate transactional booking email dispatch [{$canonicalType}]", [
                'booking_id' => $this->bookingId,
                'idempotency_key' => $idempotencyKey,
            ]);

            return;
        }

        $booking = Booking::with('bookableItem')->find($this->bookingId);

        if (! $booking) {
            Log::warning("Cannot dispatch transactional email; booking {$this->bookingId} not found.");
            Cache::forget($idempotencyKey);

            return;
        }

        $success = match ($canonicalType) {
            'booking-confirmation' => $mailService->sendBookingConfirmation($booking),
            'payment-received' => $mailService->sendPaymentReceived($booking),
            'booking-notification' => $mailService->sendBookingNotification($booking),
            'booking-cancellation' => $mailService->sendBookingCancellation(
                $booking,
                CancellationReason::tryFrom($this->payload['reason'] ?? '') ?? CancellationReason::PaymentExpired
            ),
            'payment-failed' => $mailService->sendPaymentFailed(
                $booking,
                $this->payload['reason'] ?? null
            ),
            default => false,
        };

        if (! $success) {
            // In case of delivery failure, release the cache lock so retries can re-attempt
            Cache::forget($idempotencyKey);
            Log::warning("Transactional email delivery failed [{$this->emailType}] for booking {$booking->reference}.");
        }
    }
}
