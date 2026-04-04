<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'category_id',
        'description',
        'contact_phone',
        'contact_email',
        // 👇 add these
        'country',
        'state',
        'district',
        'taluka',
        'city',
        'pincode',
        'website',
        'verification_status',
        'subscription_status',
        'subscription_expires_at',
        'job_posting_limit',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'status',
        'opening_time',
        'closing_time',
        'photo',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the business
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the business category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    /**
     * Get the products for the business
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the services for the business
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the locations for the business
     */
    public function locations(): HasMany
    {
        return $this->hasMany(BusinessLocation::class);
    }

    /**
     * Get the reviews for the business
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class);
    }

    /**
     * Get the job postings for the business
     */
    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    /**
     * Get business registration transactions for this business owner.
     */
    public function businessRegistrationTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id')
            ->where('purpose', 'business_registration');
    }

    /**
     * Get a simple payment status for this business.
     */
    public function getBusinessPaymentStatusAttribute(): string
    {
        if ($this->businessRegistrationTransactions()->where('status', 'completed')->exists()) {
            return 'paid';
        }

        if ($this->businessRegistrationTransactions()->where('status', 'pending')->exists()) {
            return 'pending';
        }

        return 'unpaid';
    }

    /**
     * Determine whether business payment is pending.
     */
    public function getPaymentPendingAttribute(): bool
    {
        return $this->businessPaymentStatus !== 'paid';
    }

    /**
     * Scope for approved businesses
     */
    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActiveSubscription($query)
    {
        return $query->where('subscription_status', 'active')
                    ->where('subscription_expires_at', '>', now());
    }
}
