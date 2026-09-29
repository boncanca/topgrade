<?php

use App\Models\Content;
use App\Models\ContentType;
use App\Models\Moment;
use App\Models\Staff;
use App\Models\Team;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Testing\AssertableInertia as Assert;

test('runtime media present resolves to media library storage URL', function () {
    $team = Team::create([
        'name' => 'U7 – U8',
        'slug' => 'u7-u8',
        'age_group' => 'U7–U8',
        'stage' => 'Foundation Phase',
        'description' => 'Test description',
        'visual_variant' => 'foundation',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $file = UploadedFile::fake()->image('custom_squad.jpg');
    $media = $team->addMedia($file)->toMediaCollection('image');

    expect(file_exists($media->getPath()))->toBeTrue();

    $resolvedUrl = $team->getResolvedMediaUrl('image');
    expect($resolvedUrl)->toBe($media->getUrl())
        ->and($resolvedUrl)->toContain('/storage/');
});

test('runtime media absent resolves to domain canonical asset', function () {
    $team = Team::create([
        'name' => 'U7 – U8',
        'slug' => 'u7-u8',
        'age_group' => 'U7–U8',
        'stage' => 'Foundation Phase',
        'description' => 'Test description',
        'visual_variant' => 'foundation',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    expect($team->getResolvedMediaUrl('image'))->toBe('/images/club/youth_match_action.jpg');

    $staff = Staff::create([
        'name' => 'Richard Matey Opoku',
        'slug' => 'richard-matey-opoku',
        'role' => 'Head Coach',
        'bio' => 'Test bio',
        'qualifications' => 'FA Coach',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    expect($staff->getResolvedMediaUrl('photo'))->toBe('/images/club/club-coach-mentoring.jpg');

    $pageType = ContentType::firstOrCreate(['slug' => 'page'], [
        'name' => 'Page',
        'kind' => 'collection',
        'template' => 'default',
        'is_system' => true,
        'is_active' => true,
    ]);

    $home = Content::create([
        'content_type_id' => $pageType->id,
        'title' => 'Home Page',
        'slug' => 'home',
        'excerpt' => 'Excerpt',
        'content' => 'Content',
        'status' => 'published',
        'published_at' => now(),
    ]);

    expect($home->getResolvedMediaUrl('videos'))->toBe('/topgrade-video.mp4')
        ->and($home->getResolvedMediaUrl('images'))->toBe('/images/club/hero-football.jpg');
});

test('runtime media record exists but physical file is missing logs warning and falls back cleanly', function () {
    Log::spy();

    $team = Team::create([
        'name' => 'U7 – U8',
        'slug' => 'u7-u8',
        'age_group' => 'U7–U8',
        'stage' => 'Foundation Phase',
        'description' => 'Test description',
        'visual_variant' => 'foundation',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $file = UploadedFile::fake()->image('missing_later.jpg');
    $media = $team->addMedia($file)->toMediaCollection('image');

    // Artificially delete physical file to simulate missing volume mount or corrupted disk
    File::delete($media->getPath());
    expect(file_exists($media->getPath()))->toBeFalse();

    $resolvedUrl = $team->getResolvedMediaUrl('image');

    // Expect fallback to canonical asset
    expect($resolvedUrl)->toBe('/images/club/youth_match_action.jpg');

    // Expect log warning triggered for storage integrity issue
    Log::shouldHaveReceived('warning')
        ->once()
        ->withArgs(fn (string $message) => str_contains($message, 'physical file is missing'));
});

test('admin-created Moment without media produces no fabricated fallback', function () {
    $moment = Moment::create([
        'title' => 'Admin Uploaded Gallery Without Image',
        'slug' => 'custom-admin-tournament-2026',
        'description' => 'Admin created a moment without uploading files yet.',
        'status' => 'published',
        'published_at' => now(),
        'featured' => true,
        'sort_order' => 1,
    ]);

    expect($moment->isSeeded())->toBeFalse()
        ->and($moment->getResolvedCoverUrl())->toBeNull()
        ->and($moment->getResolvedGallery())->toBeEmpty();
});

test('seeded Moment without runtime media resolves to canonical assets', function () {
    $moment = Moment::create([
        'title' => 'Midweek Technical Training',
        'slug' => 'midweek-technical-training',
        'description' => 'Youth squad training session.',
        'status' => 'published',
        'published_at' => now(),
        'featured' => true,
        'sort_order' => 1,
    ]);

    expect($moment->isSeeded())->toBeTrue()
        ->and($moment->getResolvedCoverUrl())->toBe('/images/club/club-training-london.jpg');

    $gallery = $moment->getResolvedGallery();
    expect($gallery)->toHaveCount(2)
        ->and($gallery[0]['url'])->toBe('/images/club/club-training-london.jpg')
        ->and($gallery[1]['url'])->toBe('/images/club/training-agility-drills.jpg');
});

test('existing frontend response shape remains completely unchanged', function () {
    $pageType = ContentType::firstOrCreate(['slug' => 'page'], [
        'name' => 'Page',
        'kind' => 'collection',
        'template' => 'default',
        'is_system' => true,
        'is_active' => true,
    ]);

    Content::firstOrCreate(
        ['slug' => 'home'],
        [
            'content_type_id' => $pageType->id,
            'title' => 'TopGrade London FC',
            'excerpt' => 'Youth football club in London.',
            'content' => 'Structured youth football training.',
            'status' => 'published',
            'published_at' => now(),
        ]
    );

    Team::firstOrCreate(
        ['slug' => 'u7-u8'],
        [
            'name' => 'U7 – U8',
            'age_group' => 'U7–U8',
            'stage' => 'Foundation Phase',
            'description' => 'First touch and agility.',
            'visual_variant' => 'foundation',
            'sort_order' => 1,
            'is_active' => true,
        ]
    );

    Staff::firstOrCreate(
        ['slug' => 'richard-matey-opoku'],
        [
            'name' => 'Richard Matey Opoku',
            'role' => 'Head Coach',
            'bio' => 'Dedicated coaching staff.',
            'qualifications' => 'FA Licensed Youth Coach',
            'sort_order' => 1,
            'is_active' => true,
        ]
    );

    $seededMoment = Moment::firstOrCreate(
        ['slug' => 'midweek-technical-training'],
        [
            'title' => 'Midweek Technical Training',
            'description' => 'Youth squad training.',
            'status' => 'published',
            'published_at' => now(),
            'featured' => true,
            'sort_order' => 1,
        ]
    );

    // 1. Home Page contract
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->has('hero.video_url')
            ->where('hero.video_url', '/topgrade-video.mp4')
            ->has('hero.poster_url')
            ->where('hero.poster_url', '/images/club/hero-football.jpg')
            ->has('squads.0.image_url')
            ->where('squads.0.image_url', '/images/club/youth_match_action.jpg')
            ->has('momentsRibbon.0.image_url')
            ->where('momentsRibbon.0.image_url', '/images/club/club-training-london.jpg')
        );

    // 2. About Page contract
    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/About')
            ->has('staff.0.photo_url')
            ->where('staff.0.photo_url', '/images/club/club-coach-mentoring.jpg')
        );

    // 3. Moments Index contract
    $this->get('/moments')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Moments/Index')
            ->has('featured')
            ->where('featured.cover_url', '/images/club/club-training-london.jpg')
            ->has('moments.0.cover_url')
            ->where('moments.0.cover_url', '/images/club/club-training-london.jpg')
        );

    // 4. Moments Show contract
    $this->get("/moments/{$seededMoment->slug}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Moments/Show')
            ->has('moment.cover_url')
            ->where('moment.cover_url', '/images/club/club-training-london.jpg')
            ->has('gallery.0.url')
            ->where('gallery.0.url', '/images/club/club-training-london.jpg')
            ->where('gallery.0.is_cover', true)
        );
});
