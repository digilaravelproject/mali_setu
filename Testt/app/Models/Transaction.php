<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'purpose',
        'razorpay_payment_id',
        'razorpay_order_id',
        'status',
        'subscription_period',
        'receipt_url',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'subscription_period' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the formatted amount attribute.
     */
    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get the purpose label attribute.
     */
    public function getPurposeLabelAttribute()
    {
        return match($this->purpose) {
            'business_registration' => 'Business Registration',
            'matrimony_profile' => 'Matrimony Profile',
            'donation' => 'Donation',
            default => ucfirst(str_replace('_', ' ', $this->purpose))
        };
    }

    /**
     * Scope a query to only include donation transactions.
     */
    public function scopeDonations($query)
    {
        return $query->where('purpose', 'donation');
    }

    /**
     * Get the related donation if this is a donation transaction.
     */
    public function donation()
    {
        if ($this->purpose === 'donation' && isset($this->metadata['donation_id'])) {
            return Donation::find($this->metadata['donation_id']);
        }
        return null;
    }

    /**
     * Get the donation cause if this is a donation transaction.
     */
    public function donationCause()
    {
        if ($this->purpose === 'donation' && isset($this->metadata['cause_id'])) {
            return DonationCause::find($this->metadata['cause_id']);
        }
        return null;
    }

    /**
     * Check if this transaction is for a donation.
     */
    public function isDonation(): bool
    {
        return $this->purpose === 'donation';
    }

    /**
     * Get donation details from metadata.
     */
    public function getDonationDetailsAttribute()
    {
        if (!$this->isDonation()) {
            return null;
        }

        return [
            'donation_id' => $this->metadata['donation_id'] ?? null,
            'cause_id' => $this->metadata['cause_id'] ?? null,
            'cause_title' => $this->metadata['cause_title'] ?? null,
        ];
    }
}