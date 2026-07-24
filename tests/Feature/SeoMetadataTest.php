<?php

use App\Models\Content;
use App\Models\ContentType;

beforeEach(function () {
    $this->contentType = ContentType::create([
        'name' => 'Page',
        'slug' => 'page',
        'kind' => 'collection',
        'template' => 'default',
        'is_system' => true,
        'is_active' => true,
    ]);
});

test('can create and associate polymorphic seo record', function () {
    $content = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'SEO Test Page',
        'slug' => 'seo-test-page',
        'content' => 'Test body',
        'status' => 'published',
    ]);

    $content->seo()->create([
        'title' => 'Custom SEO Title',
        'description' => 'Custom SEO Description',
        'canonical_url' => 'https://example.com/custom',
    ]);

    $content->load('seo');

    expect($content->seo)->not->toBeNull();
    expect($content->seo->title)->toBe('Custom SEO Title');
    expect($content->seo->description)->toBe('Custom SEO Description');
    expect($content->seo->canonical_url)->toBe('https://example.com/custom');

    // Verify database entries
    $this->assertDatabaseHas('seo_metadata', [
        'seoable_type' => 'App\Models\Content',
        'seoable_id' => $content->id,
        'title' => 'Custom SEO Title',
        'description' => 'Custom SEO Description',
        'canonical_url' => 'https://example.com/custom',
    ]);
});

test('updates polymorphic seo record', function () {
    $content = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'SEO Test Page',
        'slug' => 'seo-test-page',
        'content' => 'Test body',
        'status' => 'published',
    ]);

    $seo = $content->seo()->create([
        'title' => 'Original SEO Title',
    ]);

    expect($content->load('seo')->seo->title)->toBe('Original SEO Title');

    $seo->update([
        'title' => 'Updated SEO Title',
        'description' => 'Added Description',
    ]);

    $content->load('seo');

    expect($content->seo->title)->toBe('Updated SEO Title');
    expect($content->seo->description)->toBe('Added Description');
});
