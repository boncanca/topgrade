<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('dev admin can view audit and system logs', function () {
    $dev = User::factory()->dev()->create();

    activity()
        ->causedBy($dev)
        ->log('Dev checked dashboard settings');

    $response = $this->actingAs($dev)->get(route('dashboard.logs.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Logs/Index')
            ->has('auditLogs.data')
            ->has('systemLogs')
            ->has('systemLogSize')
            ->has('filters')
        );
});

test('dev admin can clear system logs', function () {
    $dev = User::factory()->dev()->create();

    $response = $this->actingAs($dev)->post(route('dashboard.logs.clear'), [
        'type' => 'system',
    ]);

    $response->assertRedirect(route('dashboard.logs.index', ['tab' => 'system']));
    $response->assertSessionHas('success');
});

test('dev admin can clear audit logs', function () {
    $dev = User::factory()->dev()->create();

    activity()->log('Temporary log for test');

    $response = $this->actingAs($dev)->post(route('dashboard.logs.clear'), [
        'type' => 'audit',
    ]);

    $response->assertRedirect(route('dashboard.logs.index', ['tab' => 'audit']));
    $response->assertSessionHas('success');
});

test('regular admin cannot view logs and receives 403 forbidden', function () {
    $admin = User::factory()->admin()->create(['role' => 'admin', 'is_system_account' => false]);

    $response = $this->actingAs($admin)->get(route('dashboard.logs.index'));

    $response->assertForbidden();
});

test('regular admin cannot clear logs and receives 403 forbidden', function () {
    $admin = User::factory()->admin()->create(['role' => 'admin', 'is_system_account' => false]);

    $response = $this->actingAs($admin)->post(route('dashboard.logs.clear'), [
        'type' => 'audit',
    ]);

    $response->assertForbidden();
});
