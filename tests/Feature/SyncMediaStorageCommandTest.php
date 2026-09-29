<?php

use App\Models\Content;
use App\Models\ContentBlock;
use Database\Seeders\Club\StaffSeeder;
use Database\Seeders\Club\TeamsSeeder;
use Database\Seeders\Content\MomentsSeeder;
use Database\Seeders\Content\PagesSeeder;
use Database\Seeders\ContentTypeSeeder;
use Illuminate\Support\Facades\File;

test('media:sync-storage command executes successfully and is idempotent', function () {
    $this->seed([
        ContentTypeSeeder::class,
        PagesSeeder::class,
        StaffSeeder::class,
        TeamsSeeder::class,
        MomentsSeeder::class,
    ]);

    $this->artisan('media:sync-storage')
        ->assertSuccessful();

    // Running again should also succeed idempotently
    $this->artisan('media:sync-storage')
        ->assertSuccessful();
});

test('media:sync-storage restores missing physical files without altering database records', function () {
    $this->seed([
        ContentTypeSeeder::class,
        PagesSeeder::class,
    ]);

    $this->artisan('media:sync-storage')->assertSuccessful();

    $home = Content::where('slug', 'home')->first();
    expect($home)->not->toBeNull();

    $videoMedia = $home->getMedia('videos')->first();
    expect($videoMedia)->not->toBeNull();

    $targetPath = $videoMedia->getPath();
    expect(file_exists($targetPath))->toBeTrue();

    // Temporarily delete physical file
    File::delete($targetPath);
    expect(file_exists($targetPath))->toBeFalse();

    $mediaIdBefore = $videoMedia->id;

    // Run media:sync-storage
    $this->artisan('media:sync-storage')
        ->expectsOutputToContain('RESTORED')
        ->assertSuccessful();

    // Verify physical file was restored
    expect(file_exists($targetPath))->toBeTrue();

    // Verify database record is untouched
    $videoMedia->refresh();
    expect($videoMedia->id)->toBe($mediaIdBefore);
});

test('homepage content blocks normalization migration is idempotent', function () {
    $this->seed([
        ContentTypeSeeder::class,
        PagesSeeder::class,
    ]);

    $home = Content::where('slug', 'home')->first();
    expect($home)->not->toBeNull();

    // Check that pillars were normalized to feature_list
    $pillarsCount = ContentBlock::where('content_id', $home->id)->where('type', 'pillars')->count();
    expect($pillarsCount)->toBe(0);

    // Check that quick_stats was deleted
    $quickStatsCount = ContentBlock::where('content_id', $home->id)->where('type', 'quick_stats')->count();
    expect($quickStatsCount)->toBe(0);

    // Check that hero has stats
    $hero = ContentBlock::where('content_id', $home->id)->where('type', 'hero')->first();
    expect($hero)->not->toBeNull();
    expect($hero->payload)->toHaveKey('stats');
    expect($hero->payload['stats'])->toBeArray();

    // Re-running migration up should be completely safe and idempotent
    $migration = require database_path('migrations/2026_09_29_160000_normalize_homepage_content_blocks.php');
    $migration->up();

    $hero->refresh();
    expect($hero->payload['stats'])->toBeArray();
});
