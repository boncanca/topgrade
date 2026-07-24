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
        'recurrence_pattern',
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

    public function isAvailable(): bool
    {
        $confirmedBookings = $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        return $confirmedBookings < ($this->capacity ?? $this->bookableItem->capacity);
    }
}
