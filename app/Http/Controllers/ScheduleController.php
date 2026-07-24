<?php

namespace App\Http\Controllers;

use App\Models\BookableItem;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function index(BookableItem $bookableItem): Response
    {
        $schedules = $bookableItem->schedules()
            ->orderBy('starts_at', 'desc')
            ->paginate(15);

        return Inertia::render('BookableItems/Schedules/Index', [
            'activity' => $bookableItem,
            'schedules' => $schedules,
        ]);
    }

    public function create(BookableItem $bookableItem): Response
    {
        return Inertia::render('BookableItems/Schedules/Create', [
            'activity' => $bookableItem,
        ]);
    }

    public function store(Request $request, BookableItem $bookableItem): RedirectResponse
    {
        $validated = $request->validate([
            'starts_at' => 'required|date_format:Y-m-d\TH:i',
            'ends_at' => 'required|date_format:Y-m-d\TH:i|after:starts_at',
            'capacity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'cancelled', 'full'])],
        ]);

        $validated['bookable_item_id'] = $bookableItem->id;

        Schedule::create($validated);

        return redirect()->route('bookable-items.schedules.index', $bookableItem)
            ->with('success', 'Schedule created successfully');
    }

    public function edit(BookableItem $bookableItem, Schedule $schedule): Response
    {
        return Inertia::render('BookableItems/Schedules/Edit', [
            'activity' => $bookableItem,
            'schedule' => $schedule,
        ]);
    }

    public function update(Request $request, BookableItem $bookableItem, Schedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'starts_at' => 'required|date_format:Y-m-d\TH:i',
            'ends_at' => 'required|date_format:Y-m-d\TH:i|after:starts_at',
            'capacity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'cancelled', 'full'])],
        ]);

        $schedule->update($validated);

        return redirect()->route('bookable-items.schedules.index', $bookableItem)
            ->with('success', 'Schedule updated successfully');
    }

    public function destroy(BookableItem $bookableItem, Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('bookable-items.schedules.index', $bookableItem)
            ->with('success', 'Schedule deleted successfully');
    }
}
