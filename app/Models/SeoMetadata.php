<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    use HasFactory;

    protected $table = 'seo_metadata';

    protected $fillable = [
        'title',
        'description',
        'canonical_url',
        'meta_json',
        'seoable_type',
        'seoable_id',
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
