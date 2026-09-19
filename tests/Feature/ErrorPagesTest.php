<?php

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
