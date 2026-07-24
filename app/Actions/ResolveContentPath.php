<?php

namespace App\Actions;

use App\Models\Content;

class ResolveContentPath
{
    /**
     * Resolve the content item matching the given path string.
     */
    public function execute(string $path): ?Content
    {
        $segments = array_values(array_filter(explode('/', $path)));

        if (empty($segments)) {
            // Default home route resolution
            return Content::published()
                ->whereNull('parent_id')
                ->where('slug', 'home')
                ->first();
        }

        $currentItem = null;

        foreach ($segments as $index => $segment) {
            $query = Content::published()->where('slug', $segment);

            if ($index === 0) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $currentItem->id);
            }

            $currentItem = $query->first();

            if (! $currentItem) {
                return null;
            }
        }

        return $currentItem;
    }
}
