<?php

namespace App\Http\Controllers;

use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Bookings/Index', [
            'bookings' => Booking::with(['bookableItem', 'schedule', 'contact'])
                ->orderBy('created_at', 'desc')
                ->paginate(15),
        ]);
    }

    public function show(Booking $booking): Response
    {
        return Inertia::render('Bookings/Show', [
            'booking' => $booking->load(['bookableItem', 'schedule', 'contact']),
        ]);
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'confirmed']);

        Mail::to($booking->participant_email)->send(new BookingConfirmed($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking confirmed');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'completed']);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking marked as completed');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'cancelled']);

        Mail::to($booking->participant_email)->send(new BookingCancelled($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking cancelled');
    }
}
