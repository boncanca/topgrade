<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingBookingsCommand extends Command
{
    protected $signature = 'bookings:expire-pending';

    protected $description = 'Expire pending bookings and payments that have passed their payment deadline';

    public function handle(): int
    {
        $now = now();

        $expiredBookings = Booking::where('status', BookingStatus::Pending->value)
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', $now)
            ->get();

        $count = 0;

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'status' => BookingStatus::Cancelled->value,
                    'payment_status' => PaymentStatus::Cancelled->value,
                ]);

                Payment::where('booking_id', $booking->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            });

            $count++;
        }

        $this->info("Expired {$count} pending booking(s).");

        return self::SUCCESS;
    }
}
