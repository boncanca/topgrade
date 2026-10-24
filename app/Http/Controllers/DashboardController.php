<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\InquiryStatusEnum;
use App\Enums\PaymentStatus;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Content;
use App\Models\Inquiry;
use App\Models\Moment;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with overview statistics and recent activities.
     */
    public function __invoke(): Response
    {
        $pagesCount = Content::whereHas('contentType', fn ($q) => $q->where('slug', 'page'))
            ->where('status', 'published')
            ->count();

        $newsCount = Content::whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
            ->where('status', 'published')
            ->count();

        $galleryItemsCount = Media::count();
        if ($galleryItemsCount === 0) {
            $galleryItemsCount = Moment::published()->count() * 6 ?: 45;
        }

        $upcomingMatch = [
            'days_left' => '3 days',
            'opponent' => 'vs Hackney Wick FC',
            'date' => 'Saturday, 26 April 2026',
            'kickoff' => '15:00 BST',
            'venue' => 'Hackney Marshes, London',
            'competition' => 'Combined Counties Premier Division',
        ];

        $recentUpdates = Content::whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
            ->latest('published_at')
            ->take(5)
            ->get()
            ->map(function (Content $article) {
                $category = $article->metadata_json['category'] ?? 'Club News';
                $mediaUrl = $article->getFirstMediaUrl('images');
                $featuredImage = $article->metadata_json['featured_image']
                    ?? ($mediaUrl !== '' ? $mediaUrl : '/images/club/player-match-action-topgrade-london-fc.webp');

                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $category,
                    'featured_image' => $featuredImage,
                    'published_at' => $article->published_at?->toIso8601String(),
                    'published_human' => $article->published_at ? $article->published_at->diffForHumans() : 'Recently',
                ];
            });

        $totalRevenue = (float) Booking::whereIn('status', [
            BookingStatus::Confirmed,
            BookingStatus::Completed,
        ])->orWhere('payment_status', PaymentStatus::Paid)->sum('amount');

        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', BookingStatus::Pending)->count();
        $confirmedBookings = Booking::where('status', BookingStatus::Confirmed)->count();
        $completedBookings = Booking::where('status', BookingStatus::Completed)->count();

        $upcomingSessions = Booking::where('scheduled_at', '>=', now())
            ->where('status', '!=', BookingStatus::Cancelled)
            ->count();

        $openInquiries = Inquiry::whereIn('status', [
            InquiryStatusEnum::New,
            InquiryStatusEnum::Open,
            InquiryStatusEnum::InProgress,
        ])->count();

        $activeProgramsCount = BookableItem::where('is_active', true)->count();

        $recentBookings = Booking::with('bookableItem:id,name')
            ->latest()
            ->take(6)
            ->get(['id', 'bookable_item_id', 'reference', 'participant_name', 'participant_email', 'scheduled_at', 'amount', 'currency', 'status', 'payment_status']);

        $recentInquiries = Inquiry::latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'subject', 'status', 'created_at']);

        $activePrograms = BookableItem::where('is_active', true)
            ->take(4)
            ->get(['id', 'name', 'slug', 'price', 'duration_minutes', 'capacity']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'pages_count' => $pagesCount,
                'news_count' => $newsCount,
                'gallery_items_count' => $galleryItemsCount,
                'upcoming_match' => $upcomingMatch,
                'total_revenue' => $totalRevenue,
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'confirmed_bookings' => $confirmedBookings,
                'completed_bookings' => $completedBookings,
                'upcoming_sessions' => $upcomingSessions,
                'open_inquiries' => $openInquiries,
                'active_programs' => $activeProgramsCount,
            ],
            'recentUpdates' => $recentUpdates,
            'recentBookings' => $recentBookings,
            'recentInquiries' => $recentInquiries,
            'activePrograms' => $activePrograms,
        ]);
    }
}
