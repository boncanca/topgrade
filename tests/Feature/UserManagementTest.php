<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admin can view users list', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create([
        'name' => 'Coach Smith',
        'email' => 'coach@example.com',
        'role' => 'coach',
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard.users.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->has('users.data')
            ->has('roles')
            ->has('filters')
        );
});

test('admin can view create user page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('dashboard.users.create'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Create')
            ->has('roles')
        );
});

test('admin can create a user with a specific role', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('dashboard.users.store'), [
        'name' => 'New Staff Coach',
        'email' => 'coach.new@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'role' => 'coach',
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => 'coach.new@example.com',
        'role' => 'coach',
    ]);
});

test('admin can update a user and their role', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'role' => 'editor',
    ]);

    $response = $this->actingAs($admin)->put(route('dashboard.users.update', $targetUser), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => 'coach',
    ]);

    $response->assertRedirect(route('dashboard.users.index'));

    $targetUser->refresh();
    expect($targetUser->name)->toBe('Updated Name');
    expect($targetUser->role)->toBe('coach');
});

test('admin cannot delete their own account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->delete(route('dashboard.users.destroy', $admin));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
