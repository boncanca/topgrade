<?php

namespace App\Http\Controllers;

use App\Models\Moment;
use Inertia\Inertia;
use Inertia\Response;

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
        if ($featured) {
            $gallery = $featured->getResolvedGallery();
            if (! empty($gallery)) {
                $coverUrl = $featured->getResolvedCoverUrl();
                $coverMedia = $featured->getCoverMedia();

                $featuredData = [
                    'id' => $featured->id,
                    'title' => $featured->title,
                    'slug' => $featured->slug,
                    'description' => $featured->description,
                    'published_at' => $featured->published_at?->format('d M Y'),
                    'images_count' => count($gallery),
                    'cover_url' => $coverUrl,
                    'cover_mime' => $coverMedia?->mime_type,
                    'is_video' => str_starts_with($coverMedia?->mime_type ?? '', 'video/'),
                    'images' => $gallery,
                ];
            }
        }

        $momentsList = $allPublished->map(function (Moment $m) {
            $gallery = $m->getResolvedGallery();
            $coverUrl = $m->getResolvedCoverUrl();
            $coverMedia = $m->getCoverMedia();

            return [
                'id' => $m->id,
                'title' => $m->title,
                'slug' => $m->slug,
                'description' => $m->description,
                'published_at' => $m->published_at?->format('d M Y'),
                'images_count' => count($gallery),
                'cover_url' => $coverUrl,
                'cover_mime' => $coverMedia?->mime_type,
                'featured' => $m->featured,
                'media' => $gallery,
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

        $gallery = $moment->getResolvedGallery();
        $coverUrl = $moment->getResolvedCoverUrl();

        $formattedGallery = array_map(function (array $item) use ($coverUrl) {
            return [
                'id' => $item['id'],
                'url' => $item['url'],
                'name' => $item['name'],
                'is_cover' => $coverUrl !== null && $item['url'] === $coverUrl,
            ];
        }, $gallery);

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
                'cover_url' => $m->getResolvedCoverUrl(),
                'images_count' => count($m->getResolvedGallery()),
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
                'images_count' => count($formattedGallery),
                'cover_url' => $coverUrl,
                'external_link' => $moment->external_link,
                'external_link_label' => $moment->external_link_label,
                'people' => $moment->getPublicPeople(),
                'seo' => [
                    'title' => $moment->seo?->title ?: $moment->title.' | TopGrade London FC Moments',
                    'description' => $moment->seo?->description ?: ($moment->description ?: 'TopGrade London FC matchday, training and team photography.'),
                    'canonical_url' => $moment->seo?->canonical_url,
                ],
            ],
            'gallery' => $formattedGallery,
            'related' => $related,
        ]);
    }
}
