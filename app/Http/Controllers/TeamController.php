<?php

namespace App\Http\Controllers;

use App\Models\Moment;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TeamController
{
    /**
     * Authorize developer administrator access.
     */
    private function authorizeDevAdmin(Request $request): void
    {
        if (! $request->user()?->isDevAdmin()) {
            abort(403, 'Unauthorized. Teams and squad management is currently in preview and accessible only to developer administrators.');
        }
    }

    /**
     * Display a listing of the teams.
     */
    public function index(Request $request): Response
    {
        $this->authorizeDevAdmin($request);

        $search = $request->string('search')->trim()->value();
        $active = $request->string('active')->trim()->value();

        $query = Team::query()
            ->with(['media', 'moment'])
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('age_group', 'like', "%{$search}%")
                        ->orWhere('stage', 'like', "%{$search}%");
                });
            })
            ->when($active !== '', function ($q) use ($active) {
                $q->where('is_active', $active === '1' || $active === 'true');
            })
            ->orderBy('sort_order')
            ->orderBy('name');

        $teams = $query->paginate(15)->withQueryString()->through(fn (Team $team) => [
            'id' => $team->id,
            'name' => $team->name,
            'slug' => $team->slug,
            'age_group' => $team->age_group,
            'stage' => $team->stage,
            'description' => $team->description,
            'players' => $team->players ?? [],
            'players_count' => count($team->players ?? []),
            'visual_variant' => $team->visual_variant,
            'sort_order' => $team->sort_order,
            'is_active' => (bool) $team->is_active,
            'image_url' => $team->getResolvedMediaUrl('image'),
            'moment' => $team->moment ? [
                'id' => $team->moment->id,
                'title' => $team->moment->title,
                'slug' => $team->moment->slug,
            ] : null,
            'created_at' => $team->created_at?->format('d M Y'),
        ]);

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
            'filters' => [
                'search' => $search,
                'active' => $active,
            ],
        ]);
    }

    /**
     * Show the form for creating a new team.
     */
    public function create(Request $request): Response
    {
        $this->authorizeDevAdmin($request);

        $moments = Moment::published()
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        return Inertia::render('Teams/Create', [
            'moments' => $moments,
        ]);
    }

    /**
     * Store a newly created team in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeDevAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:teams,slug'],
            'age_group' => ['required', 'string', 'max:50'],
            'stage' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'visual_variant' => ['nullable', 'string', 'max:50'],
            'moment_id' => ['nullable', 'integer', 'exists:moments,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'players' => ['nullable', 'array'],
            'players.*.name' => ['required', 'string', 'max:255'],
            'players.*.number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'players.*.position' => ['nullable', 'string', 'max:100'],
            'players.*.role' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        $team = Team::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'age_group' => $validated['age_group'],
            'stage' => $validated['stage'] ?? null,
            'description' => $validated['description'] ?? null,
            'visual_variant' => $validated['visual_variant'] ?? 'foundation',
            'moment_id' => $validated['moment_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'players' => $validated['players'] ?? [],
        ]);

        if ($request->hasFile('image')) {
            $team->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        if (function_exists('activity')) {
            activity()
                ->performedOn($team)
                ->causedBy($request->user())
                ->log("Created youth squad team: {$team->name} ({$team->age_group})");
        }

        return redirect()->route('dashboard.teams.index')
            ->with('success', "Team '{$team->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified team.
     */
    public function edit(Request $request, Team $team): Response
    {
        $this->authorizeDevAdmin($request);

        $team->load(['media', 'moment']);

        $moments = Moment::published()
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        return Inertia::render('Teams/Edit', [
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
                'age_group' => $team->age_group,
                'stage' => $team->stage,
                'description' => $team->description,
                'visual_variant' => $team->visual_variant,
                'moment_id' => $team->moment_id,
                'sort_order' => $team->sort_order,
                'is_active' => (bool) $team->is_active,
                'players' => $team->players ?? [],
                'image_url' => $team->getResolvedMediaUrl('image'),
            ],
            'moments' => $moments,
        ]);
    }

    /**
     * Update the specified team in storage.
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        $this->authorizeDevAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('teams', 'slug')->ignore($team->id)],
            'age_group' => ['required', 'string', 'max:50'],
            'stage' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'visual_variant' => ['nullable', 'string', 'max:50'],
            'moment_id' => ['nullable', 'integer', 'exists:moments,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'players' => ['nullable', 'array'],
            'players.*.name' => ['required', 'string', 'max:255'],
            'players.*.number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'players.*.position' => ['nullable', 'string', 'max:100'],
            'players.*.role' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : $team->slug;

        $team->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'age_group' => $validated['age_group'],
            'stage' => $validated['stage'] ?? null,
            'description' => $validated['description'] ?? null,
            'visual_variant' => $validated['visual_variant'] ?? 'foundation',
            'moment_id' => $validated['moment_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'players' => $validated['players'] ?? [],
        ]);

        if ($request->hasFile('image')) {
            $team->clearMediaCollection('image');
            $team->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        if (function_exists('activity')) {
            activity()
                ->performedOn($team)
                ->causedBy($request->user())
                ->log("Updated youth squad team: {$team->name} ({$team->age_group})");
        }

        return redirect()->route('dashboard.teams.index')
            ->with('success', "Team '{$team->name}' updated successfully.");
    }

    /**
     * Remove the specified team from storage.
     */
    public function destroy(Request $request, Team $team): RedirectResponse
    {
        $this->authorizeDevAdmin($request);

        $name = $team->name;
        $team->clearMediaCollection('image');
        $team->delete();

        if (function_exists('activity')) {
            activity()
                ->causedBy($request->user())
                ->log("Deleted youth squad team: {$name}");
        }

        return redirect()->route('dashboard.teams.index')
            ->with('success', "Team '{$name}' has been deleted.");
    }
}
