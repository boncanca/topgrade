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
        'status',
        'source',
    ];

    protected $casts = [
        'status' => InquiryStatusEnum::class,
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
