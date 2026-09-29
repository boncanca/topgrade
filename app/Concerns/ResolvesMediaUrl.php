<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait ResolvesMediaUrl
{
    /**
     * Resolve the public URL for a media collection, prioritizing genuine runtime uploads
     * and falling back gracefully to domain canonical defaults.
     */
    public function getResolvedMediaUrl(string $collection = 'default', ?string $fallback = null): ?string
    {
        /** @var ?Media $media */
        $media = $this->getFirstMedia($collection);

        if ($media !== null) {
            $path = $media->getPath();

            // If path is specified and file exists on disk, serve runtime media
            if (empty($path) || file_exists($path)) {
                return $media->getUrl();
            }

            // Storage integrity alert: Record exists in database, but physical file is missing from disk
            Log::warning(sprintf(
                'Media record exists [ID: %s, collection: %s] on model [%s:%s], but physical file is missing at [%s]. Falling back to canonical asset.',
                $media->id,
                $collection,
                static::class,
                $this->getKey(),
                $path
            ));
        }

        if ($fallback !== null) {
            return $fallback;
        }

        if (method_exists($this, 'getDefaultMediaUrl')) {
            return $this->getDefaultMediaUrl($collection);
        }

        return null;
    }
}
