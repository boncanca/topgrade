<?php

namespace App\Models;

use App\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Team extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, ResolvesMediaUrl;

    protected $fillable = [
        'name',
        'slug',
        'age_group',
        'stage',
        'description',
        'visual_variant',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Domain-owned canonical fallback image mapping for squad teams.
     */
    public function getDefaultMediaUrl(string $collection = 'default'): ?string
    {
        return match ($collection) {
            'image' => match ($this->slug) {
                'u7-u8' => '/images/club/u7-u8-squad-photo-topgrade-london-fc.webp',
                'u9-u10' => '/images/club/skill-acquisition-training-topgrade-london-fc.webp',
                'u11-u12' => '/images/club/game-development-match-action-topgrade-london-fc.webp',
                'u13-u14' => '/images/club/tactical-coaching-briefing-topgrade-london-fc.webp',
                'u15-u16' => '/images/club/squad-celebration-trophy-topgrade-london-fc.webp',
                default => '/images/club/foundation-phase-match-action-topgrade-london-fc.webp',
            },
            default => null,
        };
    }
}
