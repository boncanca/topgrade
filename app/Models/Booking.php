<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'bookable_item_id',
        'schedule_id',
        'contact_id',
        'reference',
        'status',
        'scheduled_at',
        'timezone',
        'participant_name',
        'participant_email',
        'participant_phone',
        'notes',
        'amount',
        'currency',
        'payment_status',
        'payment_expires_at',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'payment_expires_at' => 'datetime',
        'amount' => 'decimal:2',
        'metadata' => 'json',
        'status' => BookingStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    public function bookableItem(): BelongsTo
    {
        return $this->belongsTo(BookableItem::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Scope bookings that currently hold or reserve capacity:
     * Confirmed/completed bookings, OR pending bookings whose payment has not expired.
     */
    public function scopeActiveReservation(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereIn('status', [BookingStatus::Confirmed->value, BookingStatus::Completed->value])
                ->orWhere(function (Builder $pending) {
                    $pending->where('status', BookingStatus::Pending->value)
                        ->where(function (Builder $unexpired) {
                            $unexpired->whereNull('payment_expires_at')
                                ->orWhere('payment_expires_at', '>', now());
                        });
                });
        });
    }

    public function isReservationExpired(): bool
    {
        if ($this->status !== BookingStatus::Pending) {
            return false;
        }

        return $this->payment_expires_at !== null && $this->payment_expires_at <= now();
    }

    public function canTransitionTo(BookingStatus $target): bool
    {
        $current = $this->status instanceof BookingStatus
            ? $this->status
            : BookingStatus::from($this->status);

        return $current->canTransitionTo($target);
    }

    public function transitionTo(BookingStatus $target): void
    {
        if (! $this->canTransitionTo($target)) {
            throw new \DomainException("Cannot transition booking from {$this->status->value} to {$target->value}.");
        }

        $this->update(['status' => $target]);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'TG'.strtoupper(bin2hex(random_bytes(6)));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
}
