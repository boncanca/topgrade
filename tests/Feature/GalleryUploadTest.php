<?php

use App\Models\Moment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\FileRemover\FileBaseFileRemover;

test('gallery accepts images between 10MB and 15MB without a server error', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create();

    $this->actingAs($admin)
        ->post("/dashboard/galleries/{$moment->id}", [
            '_method' => 'put',
            'title' => $moment->title,
            'slug' => $moment->slug,
            'status' => 'draft',
            'images' => [UploadedFile::fake()->image('big.jpg', 800, 600)->size(12000)],
        ])
        ->assertSessionHasNoErrors();

    expect($moment->fresh()->getMedia('gallery'))->toHaveCount(1);
});

test('gallery rejects images over 15MB with a validation error', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create();

    $this->actingAs($admin)
        ->post("/dashboard/galleries/{$moment->id}", [
            '_method' => 'put',
            'title' => $moment->title,
            'slug' => $moment->slug,
            'status' => 'draft',
            'images' => [UploadedFile::fake()->image('huge.jpg', 800, 600)->size(16000)],
        ])
        ->assertSessionHasErrors('images.0');
});

test('gallery rejects batches exceeding 50MB total size', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create();

    // 4 files of 14MB each = 56MB total, exceeding 50MB limit
    $images = [
        UploadedFile::fake()->image('f1.jpg', 800, 600)->size(14000),
        UploadedFile::fake()->image('f2.jpg', 800, 600)->size(14000),
        UploadedFile::fake()->image('f3.jpg', 800, 600)->size(14000),
        UploadedFile::fake()->image('f4.jpg', 800, 600)->size(14000),
    ];

    $this->actingAs($admin)
        ->post("/dashboard/galleries/{$moment->id}", [
            '_method' => 'put',
            'title' => $moment->title,
            'slug' => $moment->slug,
            'status' => 'draft',
            'images' => $images,
        ])
        ->assertSessionHasErrors('images');
});

test('generic media endpoint cannot delete gallery media', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create();
    $media = $moment->addMedia(UploadedFile::fake()->image('gallery.jpg', 100, 100))->toMediaCollection('gallery');

    $this->actingAs($admin)
        ->deleteJson("/dashboard/media/{$media->uuid}")
        ->assertForbidden();

    expect($media->fresh())->not->toBeNull();
});

/**
 * Opt-in live round trip against the real Cloudinary account using genuine image payloads.
 * Run with: CLOUDINARY_LIVE_TEST=1 php artisan test --group=cloudinary-live
 */
test('cloudinary round trip with genuine image formats and bounded timeouts', function () {
    config([
        'media-library.disk_name' => 'cloudinary',
        'media-library.file_remover_class' => FileBaseFileRemover::class,
    ]);

    $moment = Moment::factory()->create();

    // Generate genuine valid images with real image headers
    $im = imagecreatetruecolor(64, 64);
    ob_start();
    imagejpeg($im);
    $jpegBytes = (string) ob_get_clean();

    ob_start();
    imagepng($im);
    $pngBytes = (string) ob_get_clean();

    ob_start();
    imagewebp($im);
    $webpBytes = (string) ob_get_clean();

    $formats = [
        'matchday.jpg' => $jpegBytes,
        'logo.png' => $pngBytes,
        'feature.webp' => $webpBytes,
    ];

    $media = [];

    try {
        foreach ($formats as $filename => $bytes) {
            $media[$filename] = $moment
                ->addMediaFromString($bytes)
                ->usingFileName($filename)
                ->toMediaCollection('gallery', 'cloudinary');
        }

        foreach ($media as $filename => $item) {
            $url = $item->getUrl();
            $response = Http::timeout(10)->get($url);

            expect($response->status())->toBe(200, "{$filename} at {$url} must return HTTP 200")
                ->and($response->header('Content-Type'))->toStartWith('image/');
        }
    } finally {
        foreach ($media as $item) {
            $item->delete();
        }
    }

    foreach ($media as $filename => $item) {
        expect(Storage::disk('cloudinary')->exists($item->getPathRelativeToRoot()))
            ->toBeFalse("{$filename} should be removed from Cloudinary storage");
    }
})->group('cloudinary-live')->skip(fn () => ! env('CLOUDINARY_LIVE_TEST'), 'Set CLOUDINARY_LIVE_TEST=1 to run live');

test('album update does not commit metadata or change draft to published if an image upload fails', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create([
        'title' => 'Original Draft Album',
        'status' => 'draft',
    ]);

    // First image uploads fine, second image triggers exception during media persistence
    $uploadCount = 0;
    Media::saving(function () use (&$uploadCount) {
        $uploadCount++;
        if ($uploadCount === 2) {
            throw new RuntimeException('Simulated storage upload failure on second photo.');
        }
    });

    $response = $this->actingAs($admin)->put("/dashboard/moments/{$moment->id}", [
        'title' => 'Should Not Be Published',
        'slug' => $moment->slug,
        'status' => 'published',
        'images' => [
            UploadedFile::fake()->image('first.jpg', 400, 300),
            UploadedFile::fake()->image('second.jpg', 400, 300),
        ],
    ]);

    $response->assertSessionHasErrors('images');

    // Crucial check: Draft status and original title are preserved; metadata was not committed
    $refreshed = $moment->fresh();
    expect($refreshed->status)->toBe('draft');
    expect($refreshed->title)->toBe('Original Draft Album');
    expect($refreshed->getMedia('gallery'))->toHaveCount(0);
});

test('nested or invalid images payload returns validation error instead of 500 server error', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->post('/dashboard/moments', [
        'title' => 'Test Nested Array',
        'status' => 'draft',
        'images' => [
            ['nested' => 'not-a-file'],
        ],
    ]);

    $response->assertSessionHasErrors(['images.0']);
    expect($response->status())->toBe(302);
});
