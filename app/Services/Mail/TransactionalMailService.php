<?php

namespace App\Services\Mail;

use App\Enums\CancellationReason;
use App\Models\Booking;

interface TransactionalMailService
{
    /**
     * Send booking confirmation email to customer (for confirmed or free bookings).
     */
    public function sendBookingConfirmation(Booking $booking): bool;

    /**
     * Send payment receipt email to customer upon successful Stripe Checkout.
     */
    public function sendPaymentReceived(Booking $booking): bool;

    /**
     * Send administrative new booking alert to the club match desk.
     */
    public function sendBookingNotification(Booking $booking): bool;

    /**
     * Send cancellation notice to customer with specific domain reason.
     */
    public function sendBookingCancellation(Booking $booking, CancellationReason $reason): bool;

    /**
     * Send payment authorization failure notice to customer.
     */
    public function sendPaymentFailed(Booking $booking, ?string $reason = null): bool;
}
