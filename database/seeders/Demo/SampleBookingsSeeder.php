<?php

namespace Database\Seeders\Demo;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\BookableItem;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class SampleBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $items = BookableItem::all();

        if ($items->isEmpty()) {
            return;
        }

        $sampleParticipants = [
            ['name' => 'Sarah Jenkins', 'email' => 'sarah.jenkins@example.com', 'phone' => '+44 7700 900077'],
            ['name' => 'Marcus Vance', 'email' => 'marcus.v@example.com', 'phone' => '+44 7700 900123'],
            ['name' => 'Elena Rostova', 'email' => 'elena.r@example.com', 'phone' => '+44 7700 900456'],
            ['name' => 'David O\'Connor', 'email' => 'doconnor@example.com', 'phone' => '+44 7700 900789'],
        ];

        $statuses = [
            ['status' => BookingStatus::Confirmed, 'payment' => PaymentStatus::Paid],
            ['status' => BookingStatus::Completed, 'payment' => PaymentStatus::Paid],
            ['status' => BookingStatus::Pending, 'payment' => PaymentStatus::Pending],
            ['status' => BookingStatus::Cancelled, 'payment' => PaymentStatus::Unpaid],
        ];

        foreach (array_values($sampleParticipants) as $index => $participant) {
            $item = $items[$index % $items->count()];
            $st = $statuses[$index % count($statuses)];
            $daysOffset = ($index - 2) * 2;

            Booking::create([
                'bookable_item_id' => $item->id,
                'reference' => Booking::generateReference(),
                'status' => $st['status'],
                'scheduled_at' => now()->addDays($daysOffset)->setHour(10 + ($index % 4))->setMinute(0),
                'timezone' => 'Europe/London',
                'participant_name' => $participant['name'],
                'participant_email' => $participant['email'],
                'participant_phone' => $participant['phone'],
                'notes' => 'Sample booking for developer testing.',
                'amount' => $item->price,
                'currency' => 'GBP',
                'payment_status' => $st['payment'],
            ]);
        }
    }
}
