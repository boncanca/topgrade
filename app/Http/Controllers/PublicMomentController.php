<?php

namespace App\Http\Controllers;

use App\Models\Moment;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicMomentController extends Controller
{
    public function index(): Response
    {
        $allPublished = Moment::published()
            ->with(['media' => fn ($q) => $q->orderBy('order_column'), 'coverMedia'])
            ->orderBy('sort_order')
            ->latest('published_at')
            ->latest('id')
            ->get();

        $featured = $allPublished->firstWhere('featured', true) ?? $allPublished->first();

        $featuredData = null;
        if ($featured && $featured->getMedia('gallery')->isNotEmpty()) {
            $cover = $featured->getCoverMedia();
            $featuredData = [
                'id' => $featured->id,
                'title' => $featured->title,
                'slug' => $featured->slug,
                'description' => $featured->description,
                'published_at' => $featured->published_at?->format('d M Y'),
                'images_count' => $featured->getMedia('gallery')->count(),
                'cover_url' => $cover?->getUrl(),
                'images' => $featured->getMedia('gallery')->map(fn (Media $m) => [
                    'id' => $m->id,
                    'url' => $m->getUrl(),
                    'name' => $m->name,
                ])->values()->all(),
            ];
        }

        $momentsList = $allPublished->map(function (Moment $m) {
            $cover = $m->getCoverMedia();

            return [
                'id' => $m->id,
                'title' => $m->title,
                'slug' => $m->slug,
                'description' => $m->description,
                'published_at' => $m->published_at?->format('d M Y'),
                'images_count' => $m->getMedia('gallery')->count(),
                'cover_url' => $cover?->getUrl(),
                'featured' => $m->featured,
            ];
        })->values()->all();

        return Inertia::render('Public/Moments/Index', [
            'featured' => $featuredData,
            'moments' => $momentsList,
        ]);
    }

    public function show(string $slug): Response
    {
        $moment = Moment::published()
            ->where('slug', $slug)
            ->with(['media' => fn ($q) => $q->orderBy('order_column'), 'coverMedia', 'seo'])
            ->firstOrFail();

        $cover = $moment->getCoverMedia();

        $gallery = $moment->getMedia('gallery')->map(fn (Media $m) => [
            'id' => $m->id,
            'url' => $m->getUrl(),
            'name' => $m->name,
            'is_cover' => $cover && $cover->id === $m->id,
        ])->values()->all();

        // Other moments for discovery
        $related = Moment::published()
            ->where('id', '!=', $moment->id)
            ->with(['media', 'coverMedia'])
            ->orderBy('sort_order')
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Moment $m) => [
                'id' => $m->id,
                'title' => $m->title,
                'slug' => $m->slug,
                'cover_url' => $m->getCoverMedia()?->getUrl(),
                'images_count' => $m->getMedia('gallery')->count(),
            ])
            ->values()
            ->all();

        return Inertia::render('Public/Moments/Show', [
            'moment' => [
                'id' => $moment->id,
                'title' => $moment->title,
                'slug' => $moment->slug,
                'description' => $moment->description,
                'published_at' => $moment->published_at?->format('d M Y'),
                'images_count' => count($gallery),
                'cover_url' => $cover?->getUrl(),
                'external_link' => $moment->external_link,
                'external_link_label' => $moment->external_link_label,
                'people' => $moment->getPublicPeople(),
                'seo' => [
                    'title' => $moment->seo?->title ?: $moment->title.' | TopGrade London FC Moments',
                    'description' => $moment->seo?->description ?: ($moment->description ?: 'TopGrade London FC matchday, training and team photography.'),
                    'canonical_url' => $moment->seo?->canonical_url,
                ],
            ],
            'gallery' => $gallery,
            'related' => $related,
        ]);
    }
}
