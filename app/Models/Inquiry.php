<?php

namespace App\Models;

use App\Enums\InquiryStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;

    protected $table = 'inquiries';

    protected $fillable = [
        'contact_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'reply_message',
        'replied_by_id',
        'status',
        'source',
        'read_at',
        'responded_at',
    ];

    protected $casts = [
        'status' => InquiryStatusEnum::class,
        'read_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by_id');
    }
}
