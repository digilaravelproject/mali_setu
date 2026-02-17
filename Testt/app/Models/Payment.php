<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'business_id',
        'matrimony_profile_id',
        'amount',
        'currency',
        'payment_method',
        'payment_gateway',
        'payment_type',
        'transaction_id',
        'gateway_payment_id',
        'status',
        'purpose',
        'description',
        'metadata',
        'refund_amount',
        'refund_reason',
        'refunded_by',
        'notes',
        'completed_at',
        'paid_at',
        'failed_at',
        'refunded_at'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'metadata' => 'array',
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
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
}
