<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
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

    public static function generateReference(): string
    {
        do {
            $reference = 'BK'.strtoupper(substr(bin2hex(random_bytes(6)), 0, 12));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
}
