<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'payment_id',
        'order_id',
        'transaction_id',
        'payment_type',
        'amount',
        'currency',
        'status',
        'payment_method',
        'razorpay_response',
        'metadata',
        'description',
        'paid_at',
        'refund_amount',
        'refunded_at',
        'refund_reason',
        'receipt_number',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'metadata' => 'array',
        'razorpay_response' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime'
    ];
    
    /**
     * Get the user that made the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the business associated with the payment
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    
    /**
     * Get the matrimony profile associated with the payment
     */
    public function matrimonyProfile()
    {
        return $this->belongsTo(MatrimonyProfile::class);
    }

    /**
     * Limit the query to payments initiated through CCAvenue.
     */
    public function scopeCCAvenue($query)
    {
        return $query->where(function ($ccavenueQuery) {
            $ccavenueQuery->whereNotNull('metadata->ccavenue_order_id')
                // Older callbacks stored an already-encoded JSON string.
                ->orWhere('metadata', 'like', '%"ccavenue_order_id"%');
        });
    }

    /**
     * Determine whether this payment was handled by CCAvenue.
     */
    public function getIsCCAvenueAttribute(): bool
    {
        $metadata = $this->metadata;

        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true) ?: [];
        }

        return is_array($metadata) && filled($metadata['ccavenue_order_id'] ?? null);
    }
    
    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    
    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    /**
     * Check if payment failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }
    
    /**
     * Check if payment is refunded
     */
    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    /**
     * Get the subscription start date
     */
    public function getSubscriptionStartDateAttribute()
    {
        return $this->paid_at ?? $this->created_at;
    }

    /**
     * Get the subscription end date
     */
    public function getSubscriptionEndDateAttribute()
    {
        $user = $this->user;
        if (!$user) {
            return null;
        }

        if ($this->payment_type === 'business_registration') {
            return $user->business?->subscription_expires_at;
        }

        if ($this->payment_type === 'matrimony_subscription') {
            return $user->matrimonyProfile?->profile_expires_at;
        }

        // Fallback checks
        if (str_contains(strtolower($this->description ?? ''), 'business')) {
            return $user->business?->subscription_expires_at;
        }
        if (str_contains(strtolower($this->description ?? ''), 'matrimony')) {
            return $user->matrimonyProfile?->profile_expires_at;
        }

        return null;
    }
}
