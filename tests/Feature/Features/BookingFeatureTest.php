<?php

use App\Models\BookableItem;
use App\Models\Booking;

test('can create bookable item', function () {
    $item = BookableItem::create([
        'name' => 'Test Training',
        'slug' => 'test-training',
        'duration_minutes' => 60,
        'location' => 'Main Ground',
        'price' => 25.00,
        'currency' => 'GBP',
        'capacity' => 15,
        'booking_label' => 'Book Now',
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('bookable_items', [
        'id' => $item->id,
        'name' => 'Test Training',
        'slug' => 'test-training',
    ]);
});

test('can create booking for bookable item', function () {
    $item = BookableItem::create([
        'name' => 'Test Trial',
        'slug' => 'test-trial',
        'duration_minutes' => 45,
        'location' => 'Training Ground',
        'price' => 10.00,
        'currency' => 'GBP',
        'capacity' => 20,
    ]);

    $booking = Booking::create([
        'bookable_item_id' => $item->id,
        'reference' => 'BK-2026-000001',
        'participant_name' => 'Jane Doe',
        'participant_email' => 'test@example.com',
        'participant_phone' => '07123456789',
        'scheduled_at' => now()->addDays(7),
        'timezone' => 'UTC',
        'status' => 'pending',
    ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'bookable_item_id' => $item->id,
        'status' => 'pending',
    ]);
});

test('booking belongs to bookable item', function () {
    $item = BookableItem::create([
        'name' => 'Test Lesson',
        'slug' => 'test-lesson',
        'duration_minutes' => 30,
        'location' => 'Academy',
        'price' => 20.00,
    ]);

    $booking = Booking::create([
        'bookable_item_id' => $item->id,
        'reference' => 'BK-2026-000002',
        'participant_name' => 'Test Player',
        'participant_email' => 'parent@example.com',
        'participant_phone' => '07123456789',
        'scheduled_at' => now()->addDays(3),
    ]);

    expect($booking->bookableItem)->not->toBeNull();
    expect($booking->bookableItem->id)->toBe($item->id);
});

test('bookable item has many bookings', function () {
    $item = BookableItem::create([
        'name' => 'Popular Session',
        'slug' => 'popular-session',
        'duration_minutes' => 60,
        'location' => 'Stadium',
        'price' => 30.00,
    ]);

    Booking::create([
        'bookable_item_id' => $item->id,
        'reference' => 'BK-2026-000003',
        'participant_name' => 'Player One',
        'participant_email' => 'one@example.com',
        'participant_phone' => '07123456789',
        'scheduled_at' => now()->addDays(5),
    ]);

    Booking::create([
        'bookable_item_id' => $item->id,
        'reference' => 'BK-2026-000004',
        'participant_name' => 'Player Two',
        'participant_email' => 'two@example.com',
        'participant_phone' => '07987654321',
        'scheduled_at' => now()->addDays(6),
    ]);

    expect($item->bookings)->toHaveCount(2);
});
