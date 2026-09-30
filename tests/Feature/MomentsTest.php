<?php

use App\Models\Moment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
});

test('unauthenticated visitors cannot access dashboard moments', function () {
    $this->get('/dashboard/moments')->assertRedirect(route('login'));
    $this->get('/dashboard/moments/create')->assertRedirect(route('login'));
});

test('non-admin users are forbidden from dashboard moments', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->getJson('/dashboard/moments')->assertForbidden();
    $this->actingAs($user)->postJson('/dashboard/moments', [])->assertForbidden();
    $this->actingAs($user)->get('/dashboard/moments')->assertRedirect(route('login'));
});

test('admin can view moments index', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Moment::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get('/dashboard/moments')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Moments/Index')
            ->has('moments.data', 3)
        );
});

test('admin can create a moment with multiple gallery images and people tags', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $images = [
        UploadedFile::fake()->image('match1.jpg', 800, 600),
        UploadedFile::fake()->image('match2.jpg', 800, 600),
        UploadedFile::fake()->image('match3.jpg', 800, 600),
    ];

    $response = $this->actingAs($admin)->post('/dashboard/moments', [
        'title' => 'Matchday — TopGrade U12 vs Hackney',
        'slug' => 'matchday-topgrade-u12-vs-hackney',
        'description' => 'A competitive Saturday morning fixture.',
        'status' => 'published',
        'published_at' => now()->format('Y-m-d\TH:i'),
        'featured' => true,
        'sort_order' => 1,
        'external_link' => 'https://example.com/match-report',
        'external_link_label' => 'Match Report',
        'people' => [
            ['name' => 'Coach Michael', 'role' => 'Head Coach', 'url' => null, 'visible' => true],
            ['name' => 'Private Scout', 'role' => 'Visitor', 'url' => null, 'visible' => false],
        ],
        'images' => $images,
        'seo' => [
            'title' => 'Matchday U12 — TopGrade London FC',
            'description' => 'Saturday morning youth league fixture gallery.',
        ],
    ]);

    $moment = Moment::where('slug', 'matchday-topgrade-u12-vs-hackney')->first();
    expect($moment)->not->toBeNull();
    expect($moment->title)->toBe('Matchday — TopGrade U12 vs Hackney');
    expect($moment->featured)->toBeTrue();
    expect($moment->getMedia('gallery'))->toHaveCount(3);
    expect($moment->cover_media_id)->not->toBeNull();
    expect($moment->cover_media_id)->toBe($moment->getFirstMedia('gallery')->id);

    $response->assertRedirect("/dashboard/moments/{$moment->id}/edit");
});

test('admin can update moment details and append more images', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create(['title' => 'Original Title']);
    $moment->addMedia(UploadedFile::fake()->image('initial.jpg'))->toMediaCollection('gallery');

    $newImage = UploadedFile::fake()->image('additional.jpg');

    $response = $this->actingAs($admin)->put("/dashboard/moments/{$moment->id}", [
        'title' => 'Updated Title',
        'slug' => $moment->slug,
        'description' => 'Updated Description',
        'status' => 'published',
        'published_at' => now()->format('Y-m-d\TH:i'),
        'featured' => false,
        'sort_order' => 2,
        'images' => [$newImage],
    ]);

    $moment->refresh();
    expect($moment->title)->toBe('Updated Title');
    expect($moment->getMedia('gallery'))->toHaveCount(2);

    $response->assertRedirect("/dashboard/moments/{$moment->id}/edit");
});

test('admin can set cover media and cannot set media from another moment', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $momentA = Moment::factory()->create();
    $mediaA1 = $momentA->addMedia(UploadedFile::fake()->image('a1.jpg'))->toMediaCollection('gallery');
    $mediaA2 = $momentA->addMedia(UploadedFile::fake()->image('a2.jpg'))->toMediaCollection('gallery');

    $momentB = Moment::factory()->create();
    $mediaB = $momentB->addMedia(UploadedFile::fake()->image('b.jpg'))->toMediaCollection('gallery');

    // Admin sets mediaA2 as cover of momentA
    $this->actingAs($admin)->put("/dashboard/moments/{$momentA->id}", [
        'title' => $momentA->title,
        'slug' => $momentA->slug,
        'status' => 'published',
        'cover_media_id' => $mediaA2->id,
    ])->assertRedirect();

    expect($momentA->fresh()->cover_media_id)->toBe($mediaA2->id);

    // Admin attempts to set mediaB (belonging to Moment B) as cover of Moment A
    $this->actingAs($admin)->put("/dashboard/moments/{$momentA->id}", [
        'title' => $momentA->title,
        'slug' => $momentA->slug,
        'status' => 'published',
        'cover_media_id' => $mediaB->id,
    ])->assertStatus(422);

    expect($momentA->fresh()->cover_media_id)->toBe($mediaA2->id);
});

test('deleting current cover image falls back to first remaining gallery image', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create();

    $media1 = $moment->addMedia(UploadedFile::fake()->image('photo1.jpg'))->toMediaCollection('gallery');
    $media2 = $moment->addMedia(UploadedFile::fake()->image('photo2.jpg'))->toMediaCollection('gallery');

    $moment->update(['cover_media_id' => $media1->id]);

    $this->actingAs($admin)->delete("/dashboard/moments/{$moment->id}/media/{$media1->id}")
        ->assertRedirect();

    $moment->refresh();
    expect($moment->getMedia('gallery'))->toHaveCount(1);
    expect($moment->cover_media_id)->toBe($media2->id);
});

test('media reordering succeeds for owned media and fails for cross-moment media', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $momentA = Moment::factory()->create();
    $m1 = $momentA->addMedia(UploadedFile::fake()->image('1.jpg'))->toMediaCollection('gallery');
    $m2 = $momentA->addMedia(UploadedFile::fake()->image('2.jpg'))->toMediaCollection('gallery');

    $momentB = Moment::factory()->create();
    $mB = $momentB->addMedia(UploadedFile::fake()->image('b.jpg'))->toMediaCollection('gallery');

    // Valid reorder
    $this->actingAs($admin)->post("/dashboard/moments/{$momentA->id}/media/reorder", [
        'order' => [$m2->id, $m1->id],
    ])->assertRedirect();

    // Cross-moment reorder attempt rejected
    $this->actingAs($admin)->post("/dashboard/moments/{$momentA->id}/media/reorder", [
        'order' => [$m2->id, $mB->id],
    ])->assertStatus(422);
});

test('deleting media belonging to another moment is forbidden', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $momentA = Moment::factory()->create();
    $mediaA = $momentA->addMedia(UploadedFile::fake()->image('a.jpg'))->toMediaCollection('gallery');

    $momentB = Moment::factory()->create();
    $mediaB = $momentB->addMedia(UploadedFile::fake()->image('b.jpg'))->toMediaCollection('gallery');

    // Attempt to delete mediaB through momentA
    $this->actingAs($admin)->delete("/dashboard/moments/{$momentA->id}/media/{$mediaB->id}")
        ->assertForbidden();

    expect($mediaB->fresh())->not->toBeNull();
});

test('deleting moment removes all associated media', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $moment = Moment::factory()->create();
    $media = $moment->addMedia(UploadedFile::fake()->image('del.jpg'))->toMediaCollection('gallery');

    $mediaId = $media->id;
    $this->assertDatabaseHas('media', ['id' => $mediaId]);

    $this->actingAs($admin)->delete("/dashboard/moments/{$moment->id}")
        ->assertRedirect('/dashboard/moments');

    $this->assertDatabaseMissing('moments', ['id' => $moment->id]);
    $this->assertDatabaseMissing('media', ['id' => $mediaId]);
});

test('public moments index shows published moments and filters out drafts', function () {
    $published = Moment::factory()->create([
        'title' => 'Published Tournament',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $published->addMedia(UploadedFile::fake()->image('p.jpg'))->toMediaCollection('gallery');

    $draft = Moment::factory()->draft()->create([
        'title' => 'Secret Training',
    ]);
    $draft->addMedia(UploadedFile::fake()->image('d.jpg'))->toMediaCollection('gallery');

    $this->get('/moments')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Moments/Index')
            ->has('moments', 1)
            ->where('moments.0.title', 'Published Tournament')
        );
});

test('public moments index provides featured moment for hero carousel', function () {
    $regular = Moment::factory()->create([
        'title' => 'Regular Gallery',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);
    $regular->addMedia(UploadedFile::fake()->image('r.jpg'))->toMediaCollection('gallery');

    $featured = Moment::factory()->featured()->create([
        'title' => 'Featured Cup Final',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $featured->addMedia(UploadedFile::fake()->image('f1.jpg'))->toMediaCollection('gallery');
    $featured->addMedia(UploadedFile::fake()->image('f2.jpg'))->toMediaCollection('gallery');

    $this->get('/moments')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Moments/Index')
            ->where('featured.title', 'Featured Cup Final')
            ->has('featured.images', 2)
        );
});

test('public moments show renders gallery and only visible people tags', function () {
    $moment = Moment::factory()->create([
        'title' => 'Behind The Scenes',
        'slug' => 'behind-the-scenes',
        'status' => 'published',
        'published_at' => now(),
        'people' => [
            ['name' => 'Coach Dan', 'role' => 'Lead Coach', 'url' => null, 'visible' => true],
            ['name' => 'Private Youth Player', 'role' => 'Midfielder', 'url' => null, 'visible' => false],
        ],
    ]);
    $moment->addMedia(UploadedFile::fake()->image('bts1.jpg'))->toMediaCollection('gallery');
    $moment->addMedia(UploadedFile::fake()->image('bts2.jpg'))->toMediaCollection('gallery');

    $this->get('/moments/behind-the-scenes')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Moments/Show')
            ->where('moment.title', 'Behind The Scenes')
            ->has('gallery', 2)
            ->has('moment.people', 1)
            ->where('moment.people.0.name', 'Coach Dan')
        );
});

test('draft moment returns 404 for public visitors', function () {
    $draft = Moment::factory()->draft()->create([
        'slug' => 'draft-gallery',
    ]);

    $this->get('/moments/draft-gallery')->assertNotFound();
});

test('nonexistent moment returns 404 for public visitors', function () {
    $this->get('/moments/does-not-exist')->assertNotFound();
});

test('admin can view galleries index at /dashboard/galleries', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Moment::factory()->count(2)->create();

    $this->actingAs($admin)
        ->get('/dashboard/galleries')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Galleries/Index')
            ->has('galleries.data', 2)
        );
});

test('admin can create and manage galleries using /dashboard/galleries endpoints', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $image = UploadedFile::fake()->image('test.jpg', 600, 400);

    $createResponse = $this->actingAs($admin)->post('/dashboard/galleries', [
        'title' => 'Academy Training Showcase',
        'slug' => 'academy-training-showcase',
        'status' => 'published',
        'published_at' => now()->format('Y-m-d\TH:i'),
        'images' => [$image],
    ]);

    $gallery = Moment::where('slug', 'academy-training-showcase')->first();
    expect($gallery)->not->toBeNull();
    $createResponse->assertRedirect("/dashboard/galleries/{$gallery->id}/edit");

    $this->actingAs($admin)
        ->get("/dashboard/galleries/{$gallery->id}/edit")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Galleries/Edit')
            ->where('moment.title', 'Academy Training Showcase')
        );

    $updateResponse = $this->actingAs($admin)->put("/dashboard/galleries/{$gallery->id}", [
        'title' => 'Updated Academy Showcase',
        'slug' => 'updated-academy-showcase',
        'status' => 'published',
    ]);
    $updateResponse->assertRedirect("/dashboard/galleries/{$gallery->id}/edit");

    $deleteResponse = $this->actingAs($admin)->delete("/dashboard/galleries/{$gallery->id}");
    $deleteResponse->assertRedirect('/dashboard/galleries');
    $this->assertDatabaseMissing('moments', ['id' => $gallery->id]);
});

test('public moments index renders safely when no moments exist', function () {
    $this->get('/moments')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Moments/Index')
            ->has('moments', 0)
            ->where('featured', null)
        );
});

test('public moments index renders safely when featured moment has no images', function () {
    Moment::factory()->featured()->create([
        'title' => 'No Photo Featured Event',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->get('/moments')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Moments/Index')
            ->where('featured', null)
            ->has('moments', 1)
        );
});
