<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Menus/Index', [
            'menus' => Menu::with('items')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Menus/Create');
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $itemsData = $validated['items'] ?? [];
        unset($validated['items']);

        $menu = Menu::create($validated);

        foreach ($itemsData as $index => $item) {
            $menu->allItems()->create([
                'label' => $item['label'],
                'url' => $item['url'] ?? '/',
                'target' => $item['target'] ?? '_self',
                'sort_order' => $item['sort_order'] ?? ($index + 1),
            ]);
        }

        return redirect()->route('menus.show', $menu)
            ->with('success', 'Menu created successfully');
    }

    public function show(Menu $menu): Response
    {
        return Inertia::render('Menus/Show', [
            'menu' => $menu->load('items'),
        ]);
    }

    public function edit(Menu $menu): Response
    {
        return Inertia::render('Menus/Edit', [
            'menu' => $menu->load('items'),
        ]);
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validated();
        $itemsData = $validated['items'] ?? null;
        unset($validated['items']);

        $menu->update($validated);

        if ($itemsData !== null) {
            $menu->allItems()->delete();
            foreach ($itemsData as $index => $item) {
                $menu->allItems()->create([
                    'label' => $item['label'],
                    'url' => $item['url'] ?? '/',
                    'target' => $item['target'] ?? '_self',
                    'sort_order' => $item['sort_order'] ?? ($index + 1),
                ]);
            }
        }

        return redirect()->route('menus.show', $menu)
            ->with('success', 'Menu updated successfully');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('menus.index')
            ->with('success', 'Menu deleted successfully');
    }
}
