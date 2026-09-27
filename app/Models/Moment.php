<?php

namespace App\Models;

use App\Concerns\HasSeo;
use App\Concerns\HasSlug;
use App\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Moment extends Model implements HasMedia
{
    use HasFactory, HasSeo, HasSlug, InteractsWithMedia, Publishable;

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
