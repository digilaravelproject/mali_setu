<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrimonyReport extends Model
{
    use HasFactory;

    protected $fillable = ['reporter_id', 'reported_user_id', 'reason_key', 'details'];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    // Common aliases let the admin request-analysis table display every interaction type.
    public function sender(): BelongsTo
    {
        return $this->reporter();
    }

    public function receiver(): BelongsTo
    {
        return $this->reportedUser();
    }

    public function getStatusAttribute(): string
    {
        return 'reported';
    }

    public function getMessageAttribute(): string
    {
        return $this->reason_key;
    }

    public function getResponseMessageAttribute(): ?string
    {
        return $this->details;
    }

    public function getRespondedAtAttribute(): null
    {
        return null;
    }
}
