<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'event_id',
        'event_type',
        'summary',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
}
