<?php

namespace App\Models;

use App\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Staff extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, ResolvesMediaUrl;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'slug',
        'role',
        'bio',
        'qualifications',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Domain-owned canonical fallback image mapping for staff members.
     */
    public function getDefaultMediaUrl(string $collection = 'default'): ?string
    {
        return match ($collection) {
            'photo' => '/images/club/club-coach-mentoring.jpg',
            default => null,
        };
    }
}
