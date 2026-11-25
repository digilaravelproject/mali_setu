<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BusinessEnquiry extends Model
{
    protected $fillable = [
        'user_id', 'business_id', 'enquirable_type', 'enquirable_id',
        'price', 'description', 'status',
    ];

    protected $casts = ['price' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function enquirable(): MorphTo
    {
        return $this->morphTo();
    }
}
