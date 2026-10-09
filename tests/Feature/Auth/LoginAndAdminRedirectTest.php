<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('/login redirects to /auth/login', function () {
    $response = $this->get('/login');

    $response->assertRedirect('/auth/login');
});

test('/admin redirects to /auth/login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/auth/login');
});

test('unauthenticated users visiting /dashboard are redirected to /auth/login', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/auth/login');
});

test('user model mass assignment respects is_admin and email_verified_at', function () {
    $verifiedAt = now()->subDay();

    $user = User::create([
        'name' => 'Mass Assignment Admin',
        'email' => 'mass-admin@example.com',
        'password' => Hash::make('secret-password-123'),
        'is_admin' => true,
        'email_verified_at' => $verifiedAt,
    ]);

    expect($user->is_admin)->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->email_verified_at->toIso8601String())->toBe($verifiedAt->toIso8601String());

    $fresh = $user->fresh();
    expect($fresh->is_admin)->toBeTrue();
    expect($fresh->email_verified_at)->not->toBeNull();
});

test('verified admin can authenticate and successfully access the dashboard', function () {
    $admin = User::create([
        'name' => 'Dashboard Admin',
        'email' => 'dashboard-admin@example.com',
        'password' => Hash::make('correct-password'),
        'is_admin' => true,
        'email_verified_at' => now(),
    ]);

    $loginResponse = $this->post(route('login.store'), [
        'email' => 'dashboard-admin@example.com',
        'password' => 'correct-password',
    ]);

    $this->assertAuthenticatedAs($admin);
    $loginResponse->assertRedirect('/dashboard');

    $dashboardResponse = $this->get('/dashboard');
    $dashboardResponse->assertOk();
});

test('non-admin user cannot access dashboard and is logged out with error', function () {
    $user = User::create([
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => Hash::make('password12345'),
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('login with unverified account stays on login and returns verification notice', function () {
    User::create([
        'name' => 'Unverified Admin',
        'email' => 'unverified@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => true,
        'email_verified_at' => null,
    ]);

    $response = $this->from('/auth/login')->post('/auth/login', [
        'email' => 'unverified@example.com',
        'password' => 'password123',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/auth/login');
    $response->assertSessionHasErrors(['auth_notice_title', 'auth_notice', 'email']);
    expect(session('errors')->get('auth_notice_title')[0])->toBe('Account verification required');
    expect(session('errors')->get('auth_notice')[0])->toContain('administrator account has not been verified');
});

test('login with non-admin account stays on login and returns authorization notice', function () {
    User::create([
        'name' => 'Regular User',
        'email' => 'nonadmin@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $response = $this->from('/auth/login')->post('/auth/login', [
        'email' => 'nonadmin@example.com',
        'password' => 'password123',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/auth/login');
    $response->assertSessionHasErrors(['auth_notice_title', 'auth_notice', 'email']);
    expect(session('errors')->get('auth_notice_title')[0])->toBe('Administrator access required');
    expect(session('errors')->get('auth_notice')[0])->toContain('does not have administrator permissions');
});

test('login with non-existent email stays on login and returns account not found notice and email error', function () {
    $response = $this->from('/auth/login')->post('/auth/login', [
        'email' => 'doesnotexist@example.com',
        'password' => 'anypassword',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/auth/login');
    $response->assertSessionHasErrors(['auth_notice_title', 'auth_notice', 'email']);
    expect(session('errors')->get('auth_notice_title')[0])->toBe('Account not found');
    expect(session('errors')->get('auth_notice')[0])->toContain('No administrator account was found with this email address');
    expect(session('errors')->get('email')[0])->toBe('No account was found with this email address.');
});

test('login with existing user and incorrect password stays on login and returns incorrect password notice and password error', function () {
    User::create([
        'name' => 'Existing Admin',
        'email' => 'existing@example.com',
        'password' => Hash::make('correctpassword'),
        'is_admin' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->from('/auth/login')->post('/auth/login', [
        'email' => 'existing@example.com',
        'password' => 'wrongpassword',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/auth/login');
    $response->assertSessionHasErrors(['auth_notice_title', 'auth_notice', 'password']);
    expect(session('errors')->get('auth_notice_title')[0])->toBe('Incorrect password');
    expect(session('errors')->get('auth_notice')[0])->toContain('The password you entered is incorrect');
    expect(session('errors')->get('password')[0])->toBe('The password you entered is incorrect.');
});

test('already authenticated admin visiting login redirects to dashboard', function () {
    $admin = User::create([
        'name' => 'Logged In Admin',
        'email' => 'loggedin@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get('/auth/login');

    $response->assertRedirect('/dashboard');
});

test('user seeder deterministically establishes the two verified admin accounts', function () {
    $this->artisan('db:seed', ['--class' => 'UserSeeder', '--force' => true])
        ->assertSuccessful();

    $clubAdmin = User::where('email', 'info@topgradelondonfc.co.uk')->first();
    expect($clubAdmin)->not->toBeNull();
    expect($clubAdmin->is_admin)->toBeTrue();
    expect($clubAdmin->email_verified_at)->not->toBeNull();
    expect($clubAdmin->is_system_account)->toBeFalse();

    $techAdmin = User::where('email', 'tomc@trupabranding.com')->first();
    expect($techAdmin)->not->toBeNull();
    expect($techAdmin->is_admin)->toBeTrue();
    expect($techAdmin->email_verified_at)->not->toBeNull();
    expect($techAdmin->is_system_account)->toBeTrue();
});
