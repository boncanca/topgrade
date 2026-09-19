<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'reference',
        'gateway',
        'gateway_session_id',
        'gateway_payment_intent_id',
        'status',
        'amount',
        'currency',
        'customer_email',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'PAY'.strtoupper(bin2hex(random_bytes(6)));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
}
