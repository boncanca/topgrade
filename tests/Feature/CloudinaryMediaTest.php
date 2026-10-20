<?php

use App\Models\Content;
use App\Models\ContentType;
use App\Models\User;
use App\Services\Storage\CloudinaryAdapter;
use CodebarAg\FlysystemCloudinary\FlysystemCloudinaryAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('cloudinary disk is configured in filesystems', function () {
    $cloudinaryConfig = config('filesystems.disks.cloudinary');

    expect($cloudinaryConfig)->toBeArray();
    expect($cloudinaryConfig['driver'])->toBe('cloudinary');
    expect(array_key_exists('cloud_name', $cloudinaryConfig))->toBeTrue();
    expect(array_key_exists('api_key', $cloudinaryConfig))->toBeTrue();
    expect(array_key_exists('api_secret', $cloudinaryConfig))->toBeTrue();
});

test('cloudinary disk can be resolved from Storage facade', function () {
    $disk = Storage::disk('cloudinary');

    expect($disk)->toBeInstanceOf(FilesystemAdapter::class);
    expect($disk->getAdapter())->toBeInstanceOf(CloudinaryAdapter::class);
    expect($disk->getAdapter())->toBeInstanceOf(FlysystemCloudinaryAdapter::class);
});

test('cloudinary adapter resolves .jpeg paths with canonical .jpg format suffix', function () {
    $url = Storage::disk('cloudinary')->url('16/PRINCE-ACHEAMPONG-topgradelondonfc.jpeg');

    expect($url)->toBeString()
        ->and($url)->toContain('16/PRINCE-ACHEAMPONG-topgradelondonfc.jpeg.jpg');
});

test('cloudinary adapter appends the delivery format for every image extension', function () {
    $disk = Storage::disk('cloudinary');

    expect($disk->url('10/training-agility-drills.jpg'))->toContain('10/training-agility-drills.jpg.jpg')
        ->and($disk->url('12/badge-logo.png'))->toContain('12/badge-logo.png.png')
        ->and($disk->url('12/hero.webp'))->toContain('12/hero.webp.webp')
        ->and($disk->url('12/My Photo.JPG'))->toContain('My%20Photo.JPG.jpg')
        ->and($disk->url('12/badge-logo.png'))->not->toContain('.png.jpg');
});

test('cloudinary adapter does not double-append the format', function () {
    $adapter = Storage::disk('cloudinary')->getAdapter();

    expect($adapter->getUrl('a/b.png.png'))->not->toContain('.png.png.png');
});

test('media library disk defaults to MEDIA_DISK or public', function () {
    $expectedDisk = env('MEDIA_DISK', 'public');
    expect(config('media-library.disk_name'))->toBe($expectedDisk);
});

test('content model registers images, videos, and block-images media collections', function () {
    $content = new Content;
    $content->registerMediaCollections();

    $collections = collect($content->mediaCollections)->pluck('name')->all();

    expect($collections)->toContain('images');
    expect($collections)->toContain('videos');
    expect($collections)->toContain('block-images');
});

test('content model can store image and video media via Spatie Media Library', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $contentType = ContentType::firstOrCreate(
        ['slug' => 'pages'],
        [
            'name' => 'Pages',
            'kind' => 'collection',
            'template' => 'default',
            'is_system' => true,
            'is_active' => true,
        ]
    );

    $content = Content::create([
        'content_type_id' => $contentType->id,
        'title' => 'Test Page with Media',
        'slug' => 'test-media-page',
        'content' => '<p>Test content</p>',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $image = UploadedFile::fake()->image('club-photo.jpg', 800, 600);
    $video = UploadedFile::fake()->create('hero-loop.mp4', 2048, 'video/mp4');

    $mediaImage = $content->addMedia($image)->toMediaCollection('images');
    $mediaVideo = $content->addMedia($video)->toMediaCollection('videos');

    expect($content->getFirstMedia('images'))->not->toBeNull();
    expect($content->getFirstMedia('images')->id)->toBe($mediaImage->id);
    expect($mediaImage->getUrl())->toBeString();

    expect($content->getFirstMedia('videos'))->not->toBeNull();
    expect($content->getFirstMedia('videos')->id)->toBe($mediaVideo->id);
    expect($mediaVideo->getUrl())->toBeString();
});

test('homepage supplies hero video and poster props from Spatie media', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $contentType = ContentType::firstOrCreate(
        ['slug' => 'pages'],
        [
            'name' => 'Pages',
            'kind' => 'collection',
            'template' => 'default',
            'is_system' => true,
            'is_active' => true,
        ]
    );

    $home = Content::updateOrCreate(
        ['slug' => 'home'],
        [
            'content_type_id' => $contentType->id,
            'title' => 'TopGrade London FC Home',
            'content' => '<p>Home page content</p>',
            'status' => 'published',
            'published_at' => now(),
        ]
    );

    $videoFile = UploadedFile::fake()->create('hero-drill.mp4', 1024, 'video/mp4');
    $posterFile = UploadedFile::fake()->image('hero-frame.jpg', 1920, 1080);

    $home->addMedia($videoFile)->toMediaCollection('videos');
    $home->addMedia($posterFile)->usingName('hero-poster')->toMediaCollection('images');

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->has('hero')
            ->where('hero.video_url', fn ($url) => ! empty($url))
            ->where('hero.poster_url', fn ($url) => ! empty($url))
        );
});

test('homepage supplies canonical hero props when no runtime media is attached', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->has('hero')
            ->where('hero.video_url', '/topgrade-video.mp4')
            ->where('hero.poster_url', '/images/club/hero-football.jpg')
        );
});

test('media controller upload stores block-images and returns expected contract', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $user = User::factory()->create([
        'is_admin' => true,
    ]);
    $contentType = ContentType::firstOrCreate(
        ['slug' => 'pages'],
        [
            'name' => 'Pages',
            'kind' => 'collection',
            'template' => 'default',
            'is_system' => true,
            'is_active' => true,
        ]
    );

    $content = Content::create([
        'content_type_id' => $contentType->id,
        'title' => 'Page for Block Upload',
        'slug' => 'block-upload-page',
        'content' => '<p>Block page</p>',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $file = UploadedFile::fake()->image('block-banner.jpg', 600, 400);

    $response = $this->actingAs($user)->postJson('/dashboard/media/upload', [
        'file' => $file,
        'content_id' => $content->id,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'uuid',
            'id',
            'url',
            'name',
        ]);

    expect($content->fresh()->getFirstMedia('block-images'))->not->toBeNull();
});

test('deleting content removes associated Spatie media', function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);

    $contentType = ContentType::firstOrCreate(
        ['slug' => 'pages'],
        [
            'name' => 'Pages',
            'kind' => 'collection',
            'template' => 'default',
            'is_system' => true,
            'is_active' => true,
        ]
    );

    $content = Content::create([
        'content_type_id' => $contentType->id,
        'title' => 'Page to be deleted',
        'slug' => 'delete-media-page',
        'content' => '<p>Delete page</p>',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $image = UploadedFile::fake()->image('delete-me.jpg');
    $media = $content->addMedia($image)->toMediaCollection('images');

    $mediaId = $media->id;
    $this->assertDatabaseHas('media', ['id' => $mediaId]);

    $content->delete();

    $this->assertDatabaseMissing('media', ['id' => $mediaId]);
});
