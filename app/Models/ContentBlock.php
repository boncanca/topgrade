<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContentBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'content_id',
        'type',
        'payload',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'payload' => 'array',
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (ContentBlock $block) {
            if (empty($block->uuid)) {
                $block->uuid = (string) Str::uuid();
            }
        });
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
