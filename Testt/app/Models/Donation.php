<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'user_id',
        'cause_id',
        'amount',
        'currency',
        'payment_method',
        'razorpay_payment_id',
        'razorpay_order_id',
        'status',
        'receipt_url',
        'message',
        'anonymous'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'anonymous' => 'boolean'
    ];

    /**
     * Get the user who made this donation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cause this donation is for
     */
    public function cause(): BelongsTo
    {
        return $this->belongsTo(DonationCause::class, 'cause_id');
    }

    /**
     * Scope for completed donations
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending donations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed donations
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}
