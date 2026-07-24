<?php

use App\Models\Content;
use App\Models\ContentBlock;
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

    $this->page = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'Home Page',
        'slug' => 'home',
        'content' => 'Home content',
        'status' => 'published',
    ]);
});

test('generates uuid automatically on creation', function () {
    $block = ContentBlock::create([
        'content_id' => $this->page->id,
        'type' => 'hero',
        'payload' => ['title' => 'Welcome'],
        'settings' => ['theme' => 'light'],
        'sort_order' => 0,
    ]);

    expect($block->uuid)->not->toBeNull()
        ->and(strlen($block->uuid))->toBe(36);
});

test('eager loads block relationships correctly', function () {
    ContentBlock::create([
        'content_id' => $this->page->id,
        'type' => 'hero',
        'payload' => ['title' => 'Welcome'],
    ]);

    $page = Content::with('blocks')->find($this->page->id);

    expect($page->blocks)->toHaveCount(1)
        ->and($page->blocks->first()->type)->toBe('hero')
        ->and($page->blocks->first()->payload)->toBeArray()
        ->and($page->blocks->first()->payload['title'])->toBe('Welcome');
});

test('deletes blocks on parent content cascade delete', function () {
    ContentBlock::create([
        'content_id' => $this->page->id,
        'type' => 'hero',
        'payload' => ['title' => 'Welcome'],
    ]);

    expect(ContentBlock::count())->toBe(1);

    $this->page->delete();

    expect(ContentBlock::count())->toBe(0);
});
