<?php

use App\Actions\ResolveContentPath;
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

test('resolves root pages', function () {
    $home = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'Home Page',
        'slug' => 'home',
        'content' => 'Home content',
        'status' => 'published',
    ]);

    $about = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'About Page',
        'slug' => 'about',
        'content' => 'About content',
        'status' => 'published',
    ]);

    $resolver = new ResolveContentPath;

    expect($resolver->execute('/'))->id->toBe($home->id);
    expect($resolver->execute('/about'))->id->toBe($about->id);
});

test('resolves nested pages', function () {
    $parent = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'Services',
        'slug' => 'services',
        'content' => 'Services list',
        'status' => 'published',
    ]);

    $child = Content::create([
        'content_type_id' => $this->contentType->id,
        'parent_id' => $parent->id,
        'title' => 'Web Development',
        'slug' => 'web-development',
        'content' => 'Web dev details',
        'status' => 'published',
    ]);

    $resolver = new ResolveContentPath;

    expect($resolver->execute('/services/web-development'))->id->toBe($child->id);
});

test('returns null for invalid paths', function () {
    $resolver = new ResolveContentPath;
    expect($resolver->execute('/invalid-path'))->toBeNull();
});

test('resolves slug collisions table-wide by using Spatie unique suffix', function () {
    $parent1 = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'Services',
        'slug' => 'services',
        'content' => 'Services list',
        'status' => 'published',
    ]);

    $parent2 = Content::create([
        'content_type_id' => $this->contentType->id,
        'title' => 'Industries',
        'slug' => 'industries',
        'content' => 'Industries list',
        'status' => 'published',
    ]);

    $child1 = Content::create([
        'content_type_id' => $this->contentType->id,
        'parent_id' => $parent1->id,
        'title' => 'Team',
        'slug' => 'team',
        'content' => 'Services team',
        'status' => 'published',
    ]);

    $child2 = Content::create([
        'content_type_id' => $this->contentType->id,
        'parent_id' => $parent2->id,
        'title' => 'Team', // Spatie generates 'team-1' due to unique constraint
        'slug' => 'team',
        'content' => 'Industries team',
        'status' => 'published',
    ]);

    $resolver = new ResolveContentPath;

    expect($resolver->execute('/services/team'))->id->toBe($child1->id);
    expect($resolver->execute('/industries/team-1'))->id->toBe($child2->id);
});

test('existing hardcoded booking routes are not resolved by the dynamic resolver', function () {
    $resolver = new ResolveContentPath;
    expect($resolver->execute('/bookings'))->toBeNull();
});
