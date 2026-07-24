<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\InquiryStatusEnum;
use App\Enums\PaymentStatus;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Inquiry;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with overview statistics and recent activities.
     */
    public function __invoke(): Response
    {
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
                'total_revenue' => $totalRevenue,
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'confirmed_bookings' => $confirmedBookings,
                'completed_bookings' => $completedBookings,
                'upcoming_sessions' => $upcomingSessions,
                'open_inquiries' => $openInquiries,
                'active_programs' => $activeProgramsCount,
            ],
            'recentBookings' => $recentBookings,
            'recentInquiries' => $recentInquiries,
            'activePrograms' => $activePrograms,
        ]);
    }
}
