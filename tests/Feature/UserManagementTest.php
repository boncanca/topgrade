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

test('dev admin can create user with dev role', function () {
    $dev = User::factory()->dev()->create();

    $response = $this->actingAs($dev)->post(route('dashboard.users.store'), [
        'name' => 'Secondary Dev',
        'email' => 'dev2@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'role' => 'dev',
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'dev2@example.com',
        'role' => 'dev',
        'is_system_account' => true,
        'is_admin' => true,
    ]);
});

test('regular admin cannot create user with dev role', function () {
    $admin = User::factory()->admin()->create(['role' => 'admin', 'is_system_account' => false]);

    $response = $this->actingAs($admin)->post(route('dashboard.users.store'), [
        'name' => 'Attempted Dev',
        'email' => 'attempted.dev@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'role' => 'dev',
    ]);

    $response->assertSessionHasErrors('role');
    $this->assertDatabaseMissing('users', ['email' => 'attempted.dev@example.com']);
});

test('regular admin cannot edit or delete dev account', function () {
    $admin = User::factory()->admin()->create(['role' => 'admin', 'is_system_account' => false]);
    $dev = User::factory()->dev()->create();

    $editResponse = $this->actingAs($admin)->get(route('dashboard.users.edit', $dev));
    $editResponse->assertForbidden();

    $deleteResponse = $this->actingAs($admin)->delete(route('dashboard.users.destroy', $dev));
    $deleteResponse->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $dev->id]);
});
