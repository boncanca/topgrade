<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('it auto-generates a password for technical super admin when none provided', function () {
    $this->artisan('topgrade:reset-admin')
        ->expectsOutputToContain('tomc@trupabranding.com')
        ->expectsOutputToContain('Technical Super Admin (Break-Glass)')
        ->expectsOutputToContain('TGFC-')
        ->assertSuccessful();

    $user = User::where('email', 'tomc@trupabranding.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->is_admin)->toBeTrue()
        ->and($user->is_system_account)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();
});

test('it removes legacy dev admin email and resets password', function () {
    User::factory()->create([
        'email' => 'dev@trupabranding.com',
        'is_admin' => true,
        'is_system_account' => true,
    ]);

    $this->artisan('topgrade:reset-admin', ['--password' => 'SecurePass123!'])
        ->expectsOutputToContain('tomc@trupabranding.com')
        ->expectsOutputToContain('SecurePass123!')
        ->assertSuccessful();

    expect(User::where('email', 'dev@trupabranding.com')->exists())->toBeFalse();

    $user = User::where('email', 'tomc@trupabranding.com')->first();
    expect($user)->not->toBeNull()
        ->and(Hash::check('SecurePass123!', $user->password))->toBeTrue();
});

test('it can reset club admin using club option', function () {
    $this->artisan('topgrade:reset-admin', ['--club' => true, '--password' => 'ClubAdminPass123!'])
        ->expectsOutputToContain('info@topgradelondonfc.co.uk')
        ->expectsOutputToContain('Club Administrator')
        ->assertSuccessful();

    $clubAdmin = User::where('email', 'info@topgradelondonfc.co.uk')->first();
    expect($clubAdmin)->not->toBeNull()
        ->and($clubAdmin->is_admin)->toBeTrue()
        ->and($clubAdmin->is_system_account)->toBeFalse()
        ->and(Hash::check('ClubAdminPass123!', $clubAdmin->password))->toBeTrue();
});
