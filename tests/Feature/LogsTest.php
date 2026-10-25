<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admin can view audit and system logs', function () {
    $admin = User::factory()->admin()->create();

    activity()
        ->causedBy($admin)
        ->log('Admin checked dashboard settings');

    $response = $this->actingAs($admin)->get(route('dashboard.logs.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Logs/Index')
            ->has('auditLogs.data')
            ->has('systemLogs')
            ->has('systemLogSize')
            ->has('filters')
        );
});

test('admin can clear system logs', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('dashboard.logs.clear'), [
        'type' => 'system',
    ]);

    $response->assertRedirect(route('dashboard.logs.index', ['tab' => 'system']));
    $response->assertSessionHas('success');
});

test('admin can clear audit logs', function () {
    $admin = User::factory()->admin()->create();

    activity()->log('Temporary log for test');

    $response = $this->actingAs($admin)->post(route('dashboard.logs.clear'), [
        'type' => 'audit',
    ]);

    $response->assertRedirect(route('dashboard.logs.index', ['tab' => 'audit']));
    $response->assertSessionHas('success');
});
