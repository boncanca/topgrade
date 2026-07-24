<?php

namespace App\Models;

use App\Concerns\HasBlocks;
use App\Concerns\HasSeo;
use App\Concerns\HasSlug;
use App\Concerns\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Content extends Model implements HasMedia
{
    use HasBlocks, HasFactory, HasSeo, HasSlug, InteractsWithMedia, Publishable;

    protected $table = 'content_entries';

    protected $fillable = [
        'content_type_id',
        'parent_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
        'metadata_json',
        'sort_order',
    ];

    protected $with = ['seo'];

    protected $casts = [
        'published_at' => 'datetime',
        'metadata_json' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('block-images');
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get the resolved path for the content item.
     */
    public function getPathAttribute(): string
    {
        $slugs = [$this->slug];
        $parent = $this->parent;

        while ($parent) {
            $slugs[] = $parent->slug;
            $parent = $parent->parent;
        }

        return '/'.implode('/', array_reverse($slugs));
    }
}
