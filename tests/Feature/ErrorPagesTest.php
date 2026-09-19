<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('non-existent web route renders dynamic 404 matchday error page preserving 404 status', function () {
    $response = $this->get('/non-existent-pitch-fixture-xyz');

    $response->assertStatus(404);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', 404)
    );
});

test('api or json requests preserve json error responses without rendering inertia error page', function () {
    $response = $this->getJson('/api/non-existent-endpoint');

    $response->assertStatus(404);
    $response->assertHeader('Content-Type', 'application/json');
    expect($response->getContent())->not->toContain('data-page=');
});

test('matchday error preview routes render correct status code and inertia component in testing environment', function (int $status) {
    $response = $this->get("/errors/{$status}");

    $response->assertStatus($status);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', $status)
    );
})->with([
    403,
    404,
    419,
    500,
    503,
]);

test('invalid error preview code returns 404', function () {
    $response = $this->get('/errors/999');

    $response->assertStatus(404);
});

test('authenticated admin accessing non-existent dashboard route renders standalone error page without admin shell', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->get('/dashboard/non-existent-admin-route-404');

    $response->assertStatus(404);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', 404)
    );

    // Verify response component is Error and does not embed the Volara AppLayout shell
    $content = $response->getContent();
    expect($content)->toContain('"component":"Error"');
    expect($content)->not->toContain('"component":"Dashboard"');
});

test('authenticated and unauthenticated users both receive standalone Error component on 500 status', function () {
    // Unauthenticated
    $guestResponse = $this->get('/errors/500');
    $guestResponse->assertStatus(500);
    $guestResponse->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', 500)
    );

    // Authenticated admin
    $admin = User::factory()->create(['is_admin' => true]);
    $adminResponse = $this->actingAs($admin)->get('/errors/500');
    $adminResponse->assertStatus(500);
    $adminResponse->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', 500)
    );
});
