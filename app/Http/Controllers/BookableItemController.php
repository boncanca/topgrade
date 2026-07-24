<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookableItemRequest;
use App\Http\Requests\UpdateBookableItemRequest;
use App\Models\BookableItem;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookableItemController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('BookableItems/Index', [
            'items' => BookableItem::latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('BookableItems/Create');
    }

    public function store(StoreBookableItemRequest $request): RedirectResponse
    {
        BookableItem::create($request->validated());

        return redirect()->route('bookable-items.index')
            ->with('success', 'Activity created successfully');
    }

    public function show(BookableItem $bookableItem): Response
    {
        return Inertia::render('BookableItems/Show', [
            'item' => $bookableItem,
        ]);
    }

    public function edit(BookableItem $bookableItem): Response
    {
        return Inertia::render('BookableItems/Edit', [
            'item' => $bookableItem,
        ]);
    }

    public function update(UpdateBookableItemRequest $request, BookableItem $bookableItem): RedirectResponse
    {
        $bookableItem->update($request->validated());

        return redirect()->route('bookable-items.show', $bookableItem)
            ->with('success', 'Activity updated successfully');
    }

    public function destroy(BookableItem $bookableItem): RedirectResponse
    {
        $bookableItem->delete();

        return redirect()->route('bookable-items.index')
            ->with('success', 'Activity deleted successfully');
    }
}
