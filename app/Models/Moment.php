<?php

namespace App\Models;

use App\Concerns\HasSeo;
use App\Concerns\HasSlug;
use App\Concerns\Publishable;
use App\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Moment extends Model implements HasMedia
{
    use HasFactory, HasSeo, HasSlug, InteractsWithMedia, Publishable, ResolvesMediaUrl;

    /**
     * Canonical shipped media definitions for baseline seeded moments.
     * Admin-created moments have no default fallback and strictly require uploaded runtime media.
     *
     * @var array<string, array{cover: string, gallery: array<int, string>}>
     */
    public const SEEDED_MOMENTS = [
        'midweek-technical-training' => [
            'cover' => '/images/club/club-training-london.jpg',
            'gallery' => [
                '/images/club/club-training-london.jpg',
                '/images/club/training-agility-drills.jpg',
            ],
        ],
        'matchday-saturday-league-fixture' => [
            'cover' => '/484977737_1109608517845156_6730033439051003629_n.jpg',
            'gallery' => [
                '/484977737_1109608517845156_6730033439051003629_n.jpg',
                '/images/club/youth_match_action.jpg',
                '/images/club/squad_celebration.jpg',
            ],
        ],
        'squad-mentoring-tactical-coaching' => [
            'cover' => '/images/club/tactical_coaching.jpg',
            'gallery' => [
                '/images/club/tactical_coaching.jpg',
                '/484192970_1108922961247045_3935375872642678849_n.jpg',
            ],
        ],
    ];

    protected $fillable = [

        'title',
        'slug',
        'description',
        'cover_media_id',
        'status',
        'published_at',
        'featured',
        'sort_order',
        'external_link',
        'external_link_label',
        'people',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
        'sort_order' => 'integer',
        'cover_media_id' => 'integer',
        'people' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    /**
     * Scope query to featured moments.
     *
     * @param  Builder<Moment>  $query
     * @return Builder<Moment>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Resolve the active cover media item for this moment.
     * Enforces that the cover belongs to this moment and falls back cleanly.
     */
    public function getCoverMedia(): ?Media
    {
        if ($this->cover_media_id && $this->relationLoaded('media')) {
            $matched = $this->media->first(fn (Media $m) => $m->id === $this->cover_media_id && $m->collection_name === 'gallery');
            if ($matched) {
                return $matched;
            }
        } elseif ($this->cover_media_id) {
            $cover = $this->coverMedia;
            if ($cover && (int) $cover->model_id === (int) $this->id && $cover->model_type === self::class && $cover->collection_name === 'gallery') {
                return $cover;
            }
        }

        return $this->getFirstMedia('gallery');
    }

    public function isSeeded(): bool
    {
        return array_key_exists((string) $this->slug, self::SEEDED_MOMENTS);
    }

    /**
     * Domain-owned canonical fallback media mapping for seeded moments.
     */
    public function getDefaultMediaUrl(string $collection = 'default'): ?string
    {
        if (! $this->isSeeded()) {
            return null;
        }

        return match ($collection) {
            'cover', 'gallery', 'default' => self::SEEDED_MOMENTS[$this->slug]['cover'] ?? null,
            default => null,
        };
    }

    /**
     * Resolve the public cover URL for this moment.
     * Seeded moments fall back to canonical shipped assets.
     * Admin-created moments strictly require genuine runtime media.
     */
    public function getResolvedCoverUrl(): ?string
    {
        $cover = $this->getCoverMedia();

        if ($cover !== null) {
            $path = $cover->getPath();
            if (empty($path) || file_exists($path)) {
                return $cover->getUrl();
            }

            Log::warning(sprintf(
                'Cover media record exists [ID: %s] on Moment [ID: %s, slug: %s], but physical file is missing at [%s].',
                $cover->id,
                $this->id,
                $this->slug,
                $path
            ));
        }

        // If this is a seeded moment, return canonical shipped cover asset
        if ($this->isSeeded()) {
            return self::SEEDED_MOMENTS[$this->slug]['cover'] ?? null;
        }

        // Admin-created moments strictly require uploaded runtime media
        return null;
    }

    /**
     * Resolve the gallery items for this moment.
     *
     * @return array<int, array{id: int|string, url: string, name: string, mime_type: ?string, is_video: bool}>
     */
    public function getResolvedGallery(): array
    {
        $gallery = [];
        $mediaItems = $this->getMedia('gallery');

        foreach ($mediaItems as $media) {
            $path = $media->getPath();
            if (empty($path) || file_exists($path)) {
                $gallery[] = [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->name,
                    'mime_type' => $media->mime_type,
                    'is_video' => str_starts_with($media->mime_type ?? '', 'video/'),
                ];
            } else {
                Log::warning(sprintf(
                    'Gallery media record exists [ID: %s] on Moment [ID: %s, slug: %s], but physical file is missing at [%s].',
                    $media->id,
                    $this->id,
                    $this->slug,
                    $path
                ));
            }
        }

        if (! empty($gallery)) {
            return $gallery;
        }

        if ($this->isSeeded()) {
            $canonical = self::SEEDED_MOMENTS[$this->slug]['gallery'] ?? [];

            return array_map(fn (string $url, int $index) => [
                'id' => -($index + 1),
                'url' => $url,
                'name' => pathinfo($url, PATHINFO_FILENAME),
                'mime_type' => 'image/jpeg',
                'is_video' => false,
            ], $canonical, array_keys($canonical));
        }

        return [];
    }

    /**
     * Publicly visible people tags (excluding any marked visible: false).
     *
     * @return array<int, array{name: string, role?: string|null, url?: string|null}>
     */
    public function getPublicPeople(): array
    {
        if (! is_array($this->people)) {
            return [];
        }

        return collect($this->people)
            ->filter(fn ($person) => is_array($person) && ! empty($person['name']) && ($person['visible'] ?? true) === true)
            ->map(fn ($person) => [
                'name' => (string) $person['name'],
                'role' => ! empty($person['role']) ? (string) $person['role'] : null,
                'url' => ! empty($person['url']) ? (string) $person['url'] : null,
            ])
            ->values()
            ->all();
    }
}
