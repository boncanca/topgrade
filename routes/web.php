<?php

use App\Http\Controllers\BookableItemController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StripeWebhookController;
use App\Models\BookableItem;
use App\Models\Content;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

// Public homepage & editorial
Route::get('/', [PublicBookingController::class, 'home'])->name('home');

// Stripe webhook endpoint (verified via HMAC signature and CSRF-exempt)
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])->name('webhooks.stripe');

// Public club information & governance pages
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicPageController::class, 'submitContact'])->name('contact.store');

// Privacy Policy (canonical /privacy-policy with /privacy alias)
Route::get('/privacy-policy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('/privacy', [PublicPageController::class, 'privacy']);

// Terms & Conditions (canonical /terms-and-conditions with /terms alias)
Route::get('/terms-and-conditions', [PublicPageController::class, 'terms'])->name('terms');
Route::get('/terms', [PublicPageController::class, 'terms']);

// Editorial: Articles
Route::get('/articles', [PublicBookingController::class, 'articles'])->name('articles.index');
Route::get('/articles/{slug}', [PublicBookingController::class, 'articleShow'])->name('articles.show');

// Public club activities & booking sessions
Route::get('/activities', [PublicBookingController::class, 'activities'])->name('activities.index');
Route::get('/bookings', [PublicBookingController::class, 'activities'])->name('sessions.index');
Route::get('/bookings/confirmation/{booking:reference}', [PublicBookingController::class, 'confirmation'])->name('sessions.confirmation');
Route::get('/bookings/{bookableItem:slug}', [PublicBookingController::class, 'show'])->name('sessions.show');
Route::post('/bookings', [PublicBookingController::class, 'book'])->name('sessions.book');
Route::get('/training', [PublicBookingController::class, 'training'])->name('training');

// XML Sitemap
Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('daily'))
        ->add(Url::create('/training')->setPriority(0.9)->setChangeFrequency('weekly'))
        ->add(Url::create('/bookings')->setPriority(0.9)->setChangeFrequency('weekly'))
        ->add(Url::create('/about')->setPriority(0.8)->setChangeFrequency('monthly'))
        ->add(Url::create('/articles')->setPriority(0.8)->setChangeFrequency('daily'))
        ->add(Url::create('/contact')->setPriority(0.7)->setChangeFrequency('monthly'))
        ->add(Url::create('/privacy-policy')->setPriority(0.5)->setChangeFrequency('yearly'))
        ->add(Url::create('/terms-and-conditions')->setPriority(0.5)->setChangeFrequency('yearly'));

    // Dynamic published articles
    Content::published()
        ->whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
        ->each(function ($article) use ($sitemap) {
            $sitemap->add(
                Url::create("/articles/{$article->slug}")
                    ->setLastModificationDate($article->updated_at ?? now())
                    ->setPriority(0.7)
                    ->setChangeFrequency('weekly')
            );
        });

    // Dynamic active bookable items
    BookableItem::where('is_active', true)->each(function ($item) use ($sitemap) {
        $sitemap->add(
            Url::create("/bookings/{$item->slug}")
                ->setLastModificationDate($item->updated_at ?? now())
                ->setPriority(0.7)
                ->setChangeFrequency('weekly')
        );
    });

    return $sitemap->toResponse(request());
})->name('sitemap');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        // Content management
        Route::resource('content', ContentController::class);
        Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
        Route::delete('media/{uuid}', [MediaController::class, 'destroy'])->name('media.destroy');

        // Menu management
        Route::resource('menus', MenuController::class);

        // Booking management
        Route::resource('bookable-items', BookableItemController::class);
        Route::resource('bookable-items.schedules', ScheduleController::class)->scoped();
        Route::resource('bookings', BookingController::class)->only(['index', 'show']);

        // Booking workflow
        Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

        // Contacts
        Route::resource('contacts', ContactController::class);

        // Inquiries
        Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    });
});

require __DIR__.'/settings.php';
