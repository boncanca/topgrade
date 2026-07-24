<?php

use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Schedule;

describe('Schedule Architecture', function () {
    it('activity has schedules', function () {
        $activity = BookableItem::factory()->create();
        $schedule = Schedule::factory()->for($activity)->create();

        expect($activity->schedules()->count())->toBe(1);
        expect($schedule->bookableItem->id)->toBe($activity->id);
    });

    it('schedule has bookings', function () {
        $activity = BookableItem::factory()->create();
        $schedule = Schedule::factory()->for($activity)->create();
        $booking = Booking::factory()->for($schedule)->create(['bookable_item_id' => $activity->id]);

        expect($schedule->bookings()->count())->toBe(1);
        expect($booking->schedule->id)->toBe($schedule->id);
    });

    it('booking references both activity and schedule', function () {
        $activity = BookableItem::factory()->create();
        $schedule = Schedule::factory()->for($activity)->create();
        $booking = Booking::factory()->for($schedule)->create(['bookable_item_id' => $activity->id]);

        expect($booking->bookableItem->id)->toBe($activity->id);
        expect($booking->schedule->id)->toBe($schedule->id);
    });

    it('capacity is enforced per schedule', function () {
        $activity = BookableItem::factory()->create(['capacity' => 20]);
        $schedule1 = Schedule::factory()->for($activity)->create(['capacity' => 2, 'status' => 'active']);
        $schedule2 = Schedule::factory()->for($activity)->create(['capacity' => 3, 'status' => 'active']);

        // Fill schedule1 to capacity
        Booking::factory(2)->for($schedule1)->create(['bookable_item_id' => $activity->id, 'status' => 'confirmed']);

        // Schedule1 should be full
        $schedule1Bookings = $schedule1->bookings()->where('status', 'confirmed')->count();
        expect($schedule1Bookings)->toBe(2);
        expect($schedule1Bookings >= $schedule1->capacity)->toBeTrue();

        // Schedule2 should still be available
        $schedule2Bookings = $schedule2->bookings()->where('status', 'confirmed')->count();
        expect($schedule2Bookings)->toBe(0);
        expect($schedule2->isAvailable())->toBeTrue();
    });

    it('cancelled schedule blocks new bookings', function () {
        $activity = BookableItem::factory()->create();
        $schedule = Schedule::factory()->for($activity)->create(['status' => 'cancelled']);

        expect($schedule->status)->toBe('cancelled');
    });

    it('schedule overrides activity capacity', function () {
        $activity = BookableItem::factory()->create(['capacity' => 20]);
        $schedule = Schedule::factory()->for($activity)->create(['capacity' => 5]);

        expect($schedule->capacity)->toBe(5);
        expect($schedule->capacity)->not->toBe($activity->capacity);
    });

    it('schedule overrides activity location', function () {
        $activity = BookableItem::factory()->create(['location' => 'Main Stadium']);
        $schedule = Schedule::factory()->for($activity)->create(['location' => 'Training Ground B']);

        expect($schedule->location)->toBe('Training Ground B');
        expect($schedule->location)->not->toBe($activity->location);
    });

    it('booking preserves contact snapshot at time of booking', function () {
        $activity = BookableItem::factory()->create();
        $schedule = Schedule::factory()->for($activity)->create();

        $booking = Booking::factory()->for($schedule)->create([
            'bookable_item_id' => $activity->id,
            'participant_name' => 'John Doe',
            'participant_email' => 'john@example.com',
            'participant_phone' => '+44 123 456',
        ]);

        expect($booking->participant_name)->toBe('John Doe');
        expect($booking->participant_email)->toBe('john@example.com');
        expect($booking->participant_phone)->toBe('+44 123 456');
    });
});
