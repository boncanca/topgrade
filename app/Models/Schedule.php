<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'bookable_item_id',
        'starts_at',
        'ends_at',
        'capacity',
        'location',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function bookableItem(): BelongsTo
    {
        return $this->belongsTo(BookableItem::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function effectiveCapacity(): int
    {
        return (int) ($this->capacity ?? $this->bookableItem?->capacity ?? 0);
    }

    public function bookedCount(?int $excludeBookingId = null): int
    {
        return $this->bookings()
            ->when($excludeBookingId, fn ($query) => $query->where('id', '!=', $excludeBookingId))
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->count();
    }

    public function isFull(?int $excludeBookingId = null): bool
    {
        return $this->bookedCount($excludeBookingId) >= $this->effectiveCapacity();
    }

    public function isAvailable(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->starts_at <= now()) {
            return false;
        }

        return ! $this->isFull();
    }
}
