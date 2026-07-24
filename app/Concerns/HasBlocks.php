<?php

namespace App\Concerns;

use App\Models\ContentBlock;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasBlocks
{
    /**
     * Get the content blocks for the model, ordered by sort position.
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class, 'content_id')->orderBy('sort_order');
    }
}
