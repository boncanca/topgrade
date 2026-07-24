<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
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
