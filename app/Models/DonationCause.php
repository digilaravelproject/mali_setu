<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCause extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'target_amount',
        'raised_amount',
        'urgency',
        'location',
        'organization',
        'contact_info',
        'image_url',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'contact_info' => 'array',
        'target_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Get all donations for this cause
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'cause_id');
    }

    /**
     * Get completed donations for this cause
     */
    public function completedDonations(): HasMany
    {
        return $this->hasMany(Donation::class, 'cause_id')->where('status', 'completed');
    }

    /**
     * Calculate donation progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        return min(100, ($this->raised_amount / $this->target_amount) * 100);
    }

    /**
     * Check if the cause is active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && 
               (!$this->end_date || $this->end_date >= now()->toDateString());
    }

    /**
     * Update raised amount when a donation is completed
     */
    public function updateRaisedAmount(): void
    {
        $this->raised_amount = $this->completedDonations()->sum('amount');
        $this->save();
    }
}
