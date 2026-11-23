<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrimonyBlock extends Model
{
    use HasFactory;

    protected $fillable = ['blocker_id', 'blocked_user_id'];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blockedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }

    public function sender(): BelongsTo
    {
        return $this->blocker();
    }

    public function receiver(): BelongsTo
    {
        return $this->blockedUser();
    }

    public function getStatusAttribute(): string
    {
        return 'blocked';
    }

    public function getMessageAttribute(): ?string
    {
        return null;
    }

    public function getResponseMessageAttribute(): ?string
    {
        return null;
    }

    public function getRespondedAtAttribute(): null
    {
        return null;
    }
}
