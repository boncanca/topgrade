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
                'u7-u8' => '/images/club/youth_match_action.jpg',
                'u9-u10' => '/484192970_1108922961247045_3935375872642678849_n.jpg',
                'u11-u12' => '/485087659_1108923127913695_5031817050217357486_n.jpg',
                'u13-u14' => '/images/club/tactical_coaching.jpg',
                'u15-u16' => '/images/club/squad_celebration.jpg',
                default => '/images/club/youth_match_action.jpg',
            },
            default => null,
        };
    }
}
