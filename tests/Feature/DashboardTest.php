<?php

use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated admin users can visit the dashboard and receive statistics', function () {
    $admin = User::factory()->admin()->create();

    $item = BookableItem::factory()->create(['is_active' => true]);
    Booking::factory()->create([
        'bookable_item_id' => $item->id,
        'amount' => 50.00,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);
    Inquiry::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Trial Inquiry',
        'message' => 'Hello',
        'status' => 'new',
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('stats')
            ->where('stats.total_bookings', 1)
            ->where('stats.open_inquiries', 1)
            ->where('stats.active_programs', 1)
            ->has('recentBookings', 1)
            ->has('recentInquiries', 1)
            ->has('activePrograms', 1)
        );
});

test('non admin users cannot visit the dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['email']);
});

test('unverified admins are redirected to email verification before dashboard access', function () {
    $user = User::factory()->admin()->unverified()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});
