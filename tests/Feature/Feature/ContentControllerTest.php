<?php

use App\Models\Content;
use App\Models\ContentType;
use App\Models\User;

test('content index loads successfully', function () {
    $user = User::factory()->admin()->create();
    $contentType = ContentType::factory()->create();
    Content::factory(3)->for($contentType)->create();

    $response = $this->actingAs($user)->get('/dashboard/content');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Content/Index'));
});

test('content create page renders form', function () {
    $user = User::factory()->admin()->create();
    ContentType::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard/content/create');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Content/Create'));
});

test('content edit page renders form with data', function () {
    $user = User::factory()->admin()->create();
    $contentType = ContentType::factory()->create();
    $content = Content::factory()->for($contentType)->create();

    $response = $this->actingAs($user)->get("/dashboard/content/{$content->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Content/Edit'));
});

test('content can be created', function () {
    $user = User::factory()->admin()->create();
    $contentType = ContentType::factory()->create();

    $response = $this->actingAs($user)->post('/dashboard/content', [
        'content_type_id' => $contentType->id,
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'This is test content',
        'status' => 'draft',
    ]);

    $response->assertRedirect('/dashboard/content');
    $this->assertDatabaseHas('content_entries', [
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);
});

test('content can be updated', function () {
    $user = User::factory()->admin()->create();
    $contentType = ContentType::factory()->create();
    $content = Content::factory()->for($contentType)->create();
    $newSlug = 'updated-slug-'.$content->id;

    $response = $this->actingAs($user)->put("/dashboard/content/{$content->id}", [
        'content_type_id' => $contentType->id,
        'title' => 'Updated Title',
        'slug' => $newSlug,
        'content' => 'Updated content',
        'status' => 'published',
    ]);

    $response->assertStatus(302);
    $content->refresh();
    expect($content->title)->toBe('Updated Title');
});

test('content can be deleted', function () {
    $user = User::factory()->admin()->create();
    $contentType = ContentType::factory()->create();
    $content = Content::factory()->for($contentType)->create();

    $response = $this->actingAs($user)->delete("/dashboard/content/{$content->id}");

    $response->assertRedirect('/dashboard/content');
    $this->assertDatabaseMissing('content_entries', ['id' => $content->id]);
});
