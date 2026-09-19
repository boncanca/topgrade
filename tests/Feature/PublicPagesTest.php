<?php

use Inertia\Testing\AssertableInertia as Assert;

test('homepage renders public home component', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
        );
});

test('public about page renders correctly', function () {
    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/About')
        );
});

test('public contact page renders correctly', function () {
    $this->get('/contact')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Contact')
        );
});

test('contact form submission creates contact and inquiry records', function () {
    $response = $this->post('/contact', [
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'phone' => '07700900000',
        'company' => 'Local School',
        'position' => 'Head of PE',
        'subject' => 'Youth Squad Trials',
        'message' => 'Hello, I would like to inquire about booking trials for our U11 players.',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('contacts', [
        'email' => 'jane.smith@example.com',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'phone' => '07700900000',
    ]);

    $this->assertDatabaseHas('inquiries', [
        'email' => 'jane.smith@example.com',
        'subject' => 'Youth Squad Trials',
        'status' => 'new',
    ]);
});

test('safeguarding page renders correctly', function () {
    $this->get('/safeguarding')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Safeguarding')
        );
});

test('accessibility page renders correctly', function () {
    $this->get('/accessibility')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Accessibility')
        );
});

test('cookies page renders correctly', function () {
    $this->get('/cookies')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Cookies')
        );
});

test('privacy page renders correctly', function () {
    $this->get('/privacy')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Privacy')
        );
});

test('terms page renders correctly', function () {
    $this->get('/terms')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Terms')
        );
});

test('training schedule page renders correctly', function () {
    $this->get('/training')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Training')
        );
});

test('sitemap xml endpoint returns valid xml with public routes', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    $content = $response->getContent();

    expect($content)->toContain('/training');
    expect($content)->toContain('/about');
    expect($content)->toContain('/safeguarding');
    expect($content)->toContain('/accessibility');
    expect($content)->toContain('/cookies');
});
