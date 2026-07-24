<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @template TModel of Model
 */
trait Publishable
{
    /**
     * Scope a query to only include published content.
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', Carbon::now());
            });
    }

    /**
     * Scope a query to only include draft content.
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include archived content.
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', 'archived');
    }

    /**
     * Check if the content is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' &&
            (is_null($this->published_at) || Carbon::parse($this->published_at)->isPast());
    }

    /**
     * Check if the content is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the content is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Mark the content as published.
     */
    public function publish(?Carbon $publishedAt = null): bool
    {
        return $this->update([
            'status' => 'published',
            'published_at' => $publishedAt ?? Carbon::now(),
        ]);
    }

    /**
     * Mark the content as archived.
     */
    public function archive(): bool
    {
        return $this->update([
            'status' => 'archived',
        ]);
    }
}
