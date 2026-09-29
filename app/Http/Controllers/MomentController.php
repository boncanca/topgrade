<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMomentRequest;
use App\Http\Requests\UpdateMomentRequest;
use App\Models\Moment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MomentController extends Controller
{
    public function index(Request $request): Response
    {
        $moments = Moment::with(['media', 'coverMedia'])
            ->latest('published_at')
            ->latest('id')
            ->paginate(15)
            ->through(function (Moment $moment) {
                $cover = $moment->getCoverMedia();

                return [
                    'id' => $moment->id,
                    'title' => $moment->title,
                    'slug' => $moment->slug,
                    'status' => $moment->status,
                    'published_at' => $moment->published_at?->toIso8601String(),
                    'featured' => $moment->featured,
                    'sort_order' => $moment->sort_order,
                    'images_count' => $moment->getMedia('gallery')->count(),
                    'cover_url' => $moment->getResolvedCoverUrl(),
                ];

            });

        $component = $request->routeIs('dashboard.moments.*') ? 'Moments/Index' : 'Galleries/Index';

        return Inertia::render($component, [
            'moments' => $moments,
            'galleries' => $moments,
        ]);
    }

    public function create(Request $request): Response
    {
        $component = $request->routeIs('dashboard.moments.*') ? 'Moments/Create' : 'Galleries/Create';

        return Inertia::render($component);
    }

    public function store(StoreMomentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $seoData = $data['seo'] ?? [];
        $images = $request->file('images', []);
        unset($data['seo'], $data['images']);

        $moment = Moment::create($data);

        if (! empty($seoData)) {
            $moment->seo()->create($seoData);
        }

        foreach ($images as $image) {
            $moment->addMedia($image)->toMediaCollection('gallery');
        }

        if (! $moment->cover_media_id && ($firstMedia = $moment->getFirstMedia('gallery'))) {
            $moment->update(['cover_media_id' => $firstMedia->id]);
        }

        $redirectRoute = $request->routeIs('dashboard.moments.*') ? 'dashboard.moments.edit' : 'dashboard.galleries.edit';

        return redirect()->route($redirectRoute, $moment)
            ->with('success', 'Gallery created successfully.');
    }

    public function edit(Request $request, Moment $moment): Response
    {
        $moment->load(['seo', 'media' => fn ($q) => $q->orderBy('order_column')]);

        $coverMedia = $moment->getCoverMedia();

        $gallery = $moment->getMedia('gallery')->map(function (Media $media) use ($coverMedia) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'url' => $media->getUrl(),
                'size' => $media->size,
                'is_cover' => $coverMedia && $coverMedia->id === $media->id,
                'order_column' => $media->order_column,
            ];
        })->values();

        $component = $request->routeIs('dashboard.moments.*') ? 'Moments/Edit' : 'Galleries/Edit';

        return Inertia::render($component, [
            'moment' => [
                'id' => $moment->id,
                'title' => $moment->title,
                'slug' => $moment->slug,
                'description' => $moment->description,
                'cover_media_id' => $coverMedia?->id,
                'status' => $moment->status,
                'published_at' => $moment->published_at?->format('Y-m-d\TH:i'),
                'featured' => $moment->featured,
                'sort_order' => $moment->sort_order,
                'external_link' => $moment->external_link,
                'external_link_label' => $moment->external_link_label,
                'people' => $moment->people ?? [],
                'seo' => [
                    'title' => $moment->seo?->title ?? '',
                    'description' => $moment->seo?->description ?? '',
                    'canonical_url' => $moment->seo?->canonical_url ?? '',
                ],
            ],
            'gallery' => $gallery,
        ]);
    }

    public function update(UpdateMomentRequest $request, Moment $moment): RedirectResponse
    {
        $data = $request->validated();
        $seoData = $data['seo'] ?? [];
        $images = $request->file('images', []);
        unset($data['seo'], $data['images']);

        if (! empty($data['cover_media_id'])) {
            $coverId = (int) $data['cover_media_id'];
            $isOwned = $moment->getMedia('gallery')->contains(fn (Media $m) => $m->id === $coverId);
            abort_unless($isOwned, 422, 'Selected cover image does not belong to this gallery.');
        }

        $moment->update($data);

        if (! empty($seoData)) {
            $moment->seo()->updateOrCreate([], $seoData);
        }

        foreach ($images as $image) {
            $moment->addMedia($image)->toMediaCollection('gallery');
        }

        // Enforce cover fallback invariant if cover is not set
        if (! $moment->cover_media_id && ($firstMedia = $moment->fresh()->getFirstMedia('gallery'))) {
            $moment->update(['cover_media_id' => $firstMedia->id]);
        }

        $redirectRoute = $request->routeIs('dashboard.moments.*') ? 'dashboard.moments.edit' : 'dashboard.galleries.edit';

        return redirect()->route($redirectRoute, $moment)
            ->with('success', 'Gallery updated successfully.');
    }

    public function reorderMedia(Request $request, Moment $moment): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        $submittedIds = $validated['order'];
        $ownedIds = $moment->getMedia('gallery')->pluck('id');

        abort_unless(
            collect($submittedIds)->every(fn ($id) => $ownedIds->contains($id)),
            422,
            'One or more media items do not belong to this gallery.'
        );

        Media::setNewOrder($submittedIds);

        return back()->with('success', 'Gallery order updated.');
    }

    public function destroyMedia(Moment $moment, Media $media): RedirectResponse
    {
        abort_unless(
            (int) $media->model_id === (int) $moment->id &&
            $media->model_type === Moment::class &&
            $media->collection_name === 'gallery',
            403,
            'This media does not belong to this gallery.'
        );

        $wasCover = (int) $moment->cover_media_id === (int) $media->id;
        $media->delete();

        if ($wasCover) {
            $remainingCover = $moment->fresh()->getFirstMedia('gallery');
            $moment->update(['cover_media_id' => $remainingCover?->id]);
        }

        return back()->with('success', 'Image removed from gallery.');
    }

    public function destroy(Request $request, Moment $moment): RedirectResponse
    {
        $moment->delete();

        $redirectRoute = $request->routeIs('dashboard.moments.*') ? 'dashboard.moments.index' : 'dashboard.galleries.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Gallery deleted successfully.');
    }
}
