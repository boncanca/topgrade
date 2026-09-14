<?php

use App\Enums\BookingStatus;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('admin can confirm a pending booking', function () {
    $booking = Booking::factory()->create(['status' => BookingStatus::Pending]);

    $response = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$booking->id}/confirm");

    $response->assertRedirect("/dashboard/bookings/{$booking->id}");
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed);
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'confirmed',
    ]);
});

test('admin can complete a confirmed booking', function () {
    $booking = Booking::factory()->create(['status' => BookingStatus::Confirmed]);

    $response = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$booking->id}/complete");

    $response->assertRedirect("/dashboard/bookings/{$booking->id}");
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Completed);
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'completed',
    ]);
});

test('admin can cancel a booking', function () {
    $booking = Booking::factory()->create(['status' => BookingStatus::Pending]);

    $response = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$booking->id}/cancel");

    $response->assertRedirect("/dashboard/bookings/{$booking->id}");
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'cancelled',
    ]);
});

test('unauthenticated user cannot confirm a booking', function () {
    $booking = Booking::factory()->create(['status' => BookingStatus::Pending]);

    $response = $this->post("/dashboard/bookings/{$booking->id}/confirm");

    $response->assertRedirect('/login');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Pending);
});

test('unauthenticated user cannot cancel a booking', function () {
    $booking = Booking::factory()->create(['status' => BookingStatus::Pending]);

    $response = $this->post("/dashboard/bookings/{$booking->id}/cancel");

    $response->assertRedirect('/login');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Pending);
});

test('completed booking cannot be confirmed or cancelled', function () {
    $booking = Booking::factory()->create(['status' => BookingStatus::Completed]);

    $responseConfirm = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$booking->id}/confirm");
    $responseConfirm->assertSessionHasErrors('status');

    $responseCancel = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$booking->id}/cancel");
    $responseCancel->assertSessionHasErrors('status');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Completed);
});

test('cancelled booking cannot be confirmed or completed', function () {
    $booking = Booking::factory()->create(['status' => BookingStatus::Cancelled]);

    $responseConfirm = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$booking->id}/confirm");
    $responseConfirm->assertSessionHasErrors('status');

    $responseComplete = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$booking->id}/complete");
    $responseComplete->assertSessionHasErrors('status');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled);
});

test('admin cannot confirm booking when schedule is at full capacity', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create([
        'capacity' => 1,
        'status' => 'active',
    ]);

    // Fill capacity with a confirmed booking
    Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Confirmed,
    ]);

    // Second booking is pending
    $pendingBooking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
    ]);

    $response = $this->actingAs($this->admin)
        ->post("/dashboard/bookings/{$pendingBooking->id}/confirm");

    $response->assertSessionHasErrors('status');

    $pendingBooking->refresh();
    expect($pendingBooking->status)->toBe(BookingStatus::Pending);
});

test('nested schedule routes prevent cross-activity manipulation', function () {
    $activity1 = BookableItem::factory()->create();
    $activity2 = BookableItem::factory()->create();
    $schedule2 = Schedule::factory()->for($activity2)->create();

    // Trying to access schedule2 via activity1's URL must 404
    $this->actingAs($this->admin)
        ->get("/dashboard/bookable-items/{$activity1->id}/schedules/{$schedule2->id}/edit")
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->put("/dashboard/bookable-items/{$activity1->id}/schedules/{$schedule2->id}", [
            'starts_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(2)->addHours(1)->format('Y-m-d\TH:i'),
            'status' => 'active',
        ])
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->delete("/dashboard/bookable-items/{$activity1->id}/schedules/{$schedule2->id}")
        ->assertNotFound();
});

test('generic booking CRUD routes are not exposed', function () {
    $this->actingAs($this->admin)
        ->get('/dashboard/bookings/create')
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->get('/dashboard/bookings/1/edit')
        ->assertNotFound();
});
