<?php

use App\Models\User;

test('/dashboard/articles redirects or provides articles index', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/dashboard/articles');

    $response->assertRedirect('/dashboard/content?type=article');
});

test('/dashboard/moments is accessible to admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/dashboard/moments');

    $response->assertOk();
});

test('/dashboard/media redirects to /dashboard/galleries', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/dashboard/media');

    $response->assertRedirect('/dashboard/galleries');
});

test('/dashboard/teams is restricted to dev admin and forbidden for regular admin', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/dashboard/teams')->assertForbidden();

    $devAdmin = User::factory()->create([
        'role' => 'dev',
        'is_admin' => true,
        'is_system_account' => true,
    ]);
    $this->actingAs($devAdmin)->get('/dashboard/teams')->assertOk();
});

test('/dashboard/fixtures redirects to /dashboard/bookings', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/dashboard/fixtures');

    $response->assertRedirect('/dashboard/bookings');
});

test('/dashboard/galleries is accessible to admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/dashboard/galleries');

    $response->assertOk();
});

test('public ghost routes redirect to canonical destinations', function () {
    $this->get('/news')->assertRedirect('/articles');
    $this->get('/gallery')->assertRedirect('/moments');
    $this->get('/fixtures')->assertRedirect('/#matchday');
});

test('public /teams page is accessible', function () {
    $response = $this->get('/teams');

    $response->assertOk();
});
