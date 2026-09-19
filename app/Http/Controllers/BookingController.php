<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
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
        if (! $booking->canTransitionTo(BookingStatus::Confirmed)) {
            throw ValidationException::withMessages([
                'status' => "Cannot confirm a booking that is currently {$booking->status->value}.",
            ]);
        }

        if ($booking->schedule_id) {
            DB::transaction(function () use ($booking) {
                /** @var Schedule|null $schedule */
                $schedule = Schedule::where('id', $booking->schedule_id)
                    ->lockForUpdate()
                    ->first();

                if ($schedule && $schedule->isFull($booking->id)) {
                    throw ValidationException::withMessages([
                        'status' => 'Cannot confirm booking: schedule is already at full capacity.',
                    ]);
                }

                $booking->transitionTo(BookingStatus::Confirmed);
            });
        } else {
            $booking->transitionTo(BookingStatus::Confirmed);
        }

        Mail::to($booking->participant_email)->send(new BookingConfirmed($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking confirmed');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        if (! $booking->canTransitionTo(BookingStatus::Completed)) {
            throw ValidationException::withMessages([
                'status' => "Cannot complete a booking that is currently {$booking->status->value}.",
            ]);
        }

        $booking->transitionTo(BookingStatus::Completed);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking marked as completed');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        if (! $booking->canTransitionTo(BookingStatus::Cancelled)) {
            throw ValidationException::withMessages([
                'status' => "Cannot cancel a booking that is currently {$booking->status->value}.",
            ]);
        }

        $booking->transitionTo(BookingStatus::Cancelled);

        Mail::to($booking->participant_email)->send(new BookingCancelled($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking cancelled');
    }
}
