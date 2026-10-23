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
            $isRemote = ! in_array($media->disk, ['public', 'local'], true)
                || config("filesystems.disks.{$media->disk}.driver", 'local') !== 'local';
            $path = $media->getPath();

            // If remote disk, empty path, or physical file exists on local disk, serve runtime media
            if ($isRemote || empty($path) || file_exists($path)) {
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
